#!/bin/bash

# 鋒兄AI資訊系統部署腳本
# 使用方法: ./deploy.sh

echo "🚀 開始部署鋒兄AI資訊系統..."

# 顏色定義
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 檢查是否為 root 用戶
if [ "$EUID" -eq 0 ]; then
    echo -e "${RED}請不要使用 root 用戶運行此腳本${NC}"
    exit 1
fi

# 檢查 PHP 版本
echo -e "${BLUE}檢查 PHP 版本...${NC}"
PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
if [ "$(echo "$PHP_VERSION >= 7.4" | bc)" -eq 1 ]; then
    echo -e "${GREEN}✓ PHP 版本: $PHP_VERSION${NC}"
else
    echo -e "${RED}✗ 需要 PHP 7.4 或更高版本，當前版本: $PHP_VERSION${NC}"
    exit 1
fi

# 檢查 MySQL
echo -e "${BLUE}檢查 MySQL 連接...${NC}"
if command -v mysql &> /dev/null; then
    echo -e "${GREEN}✓ MySQL 已安裝${NC}"
else
    echo -e "${RED}✗ MySQL 未安裝${NC}"
    exit 1
fi

# 創建必要的目錄
echo -e "${BLUE}創建目錄結構...${NC}"
mkdir -p public_html/uploads/{images,videos,thumbnails}
mkdir -p public_html/storage/{temp,cache}
mkdir -p logs

# 設置權限
echo -e "${BLUE}設置文件權限...${NC}"
chmod 755 public_html/
chmod 777 public_html/uploads/
chmod 777 public_html/storage/
chmod 777 logs/
chmod 644 public_html/.htaccess

echo -e "${GREEN}✓ 權限設置完成${NC}"

# 檢查 .env 文件
if [ ! -f "public_html/.env" ]; then
    echo -e "${YELLOW}⚠ 未找到 .env 文件，複製示例文件...${NC}"
    cp public_html/.env.example public_html/.env
    echo -e "${YELLOW}請編輯 public_html/.env 文件並配置數據庫連接${NC}"
fi

# 檢查 Apache 模組
echo -e "${BLUE}檢查 Apache 配置...${NC}"
if apache2ctl -M | grep -q "rewrite_module"; then
    echo -e "${GREEN}✓ mod_rewrite 已啟用${NC}"
else
    echo -e "${YELLOW}⚠ mod_rewrite 可能未啟用，請確保啟用此模組${NC}"
fi

# 創建數據庫表
echo -e "${BLUE}初始化數據庫...${NC}"
read -p "是否要初始化數據庫表？(y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php public_html/config/database.php
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓ 數據庫初始化完成${NC}"
    else
        echo -e "${RED}✗ 數據庫初始化失敗${NC}"
    fi
fi

# 測試 API
echo -e "${BLUE}測試 API 連接...${NC}"
if command -v curl &> /dev/null; then
    RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/api/status)
    if [ "$RESPONSE" -eq 200 ]; then
        echo -e "${GREEN}✓ API 測試成功${NC}"
    else
        echo -e "${YELLOW}⚠ API 測試失敗，HTTP 狀態碼: $RESPONSE${NC}"
    fi
else
    echo -e "${YELLOW}⚠ curl 未安裝，跳過 API 測試${NC}"
fi

# 創建日誌輪轉配置
echo -e "${BLUE}配置日誌輪轉...${NC}"
cat > logrotate.conf << EOF
logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
EOF

echo -e "${GREEN}✓ 日誌輪轉配置完成${NC}"

# 創建備份腳本
echo -e "${BLUE}創建備份腳本...${NC}"
cat > backup.sh << 'EOF'
#!/bin/bash
# 自動備份腳本

BACKUP_DIR="/backup/feng_ai_system"
DATE=$(date +%Y%m%d_%H%M%S)

# 創建備份目錄
mkdir -p $BACKUP_DIR

# 備份數據庫
mysqldump -u root -p feng_ai_system > $BACKUP_DIR/database_$DATE.sql

# 備份文件
tar -czf $BACKUP_DIR/files_$DATE.tar.gz public_html/uploads/ public_html/storage/

# 清理舊備份（保留30天）
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete

echo "備份完成: $DATE"
EOF

chmod +x backup.sh
echo -e "${GREEN}✓ 備份腳本創建完成${NC}"

# 創建系統監控腳本
echo -e "${BLUE}創建監控腳本...${NC}"
cat > monitor.sh << 'EOF'
#!/bin/bash
# 系統監控腳本

LOG_FILE="logs/monitor.log"
DATE=$(date '+%Y-%m-%d %H:%M:%S')

# 檢查磁盤空間
DISK_USAGE=$(df -h / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "[$DATE] 警告: 磁盤使用率 ${DISK_USAGE}%" >> $LOG_FILE
fi

# 檢查上傳目錄大小
UPLOAD_SIZE=$(du -sh public_html/uploads/ | cut -f1)
echo "[$DATE] 上傳目錄大小: $UPLOAD_SIZE" >> $LOG_FILE

# 檢查 API 狀態
API_STATUS=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/api/status)
if [ "$API_STATUS" -ne 200 ]; then
    echo "[$DATE] 警告: API 狀態異常 ($API_STATUS)" >> $LOG_FILE
fi

echo "[$DATE] 系統監控完成" >> $LOG_FILE
EOF

chmod +x monitor.sh
echo -e "${GREEN}✓ 監控腳本創建完成${NC}"

# 顯示部署完成信息
echo
echo -e "${GREEN}🎉 部署完成！${NC}"
echo
echo -e "${BLUE}接下來的步驟：${NC}"
echo "1. 編輯 public_html/.env 文件配置數據庫連接"
echo "2. 確保 Apache 的 mod_rewrite 模組已啟用"
echo "3. 訪問您的網站測試功能"
echo "4. 設置定時任務運行 monitor.sh 和 backup.sh"
echo
echo -e "${BLUE}有用的命令：${NC}"
echo "- 查看錯誤日誌: tail -f logs/*.log"
echo "- 測試 API: curl http://localhost/api/status"
echo "- 運行備份: ./backup.sh"
echo "- 運行監控: ./monitor.sh"
echo
echo -e "${YELLOW}注意事項：${NC}"
echo "- 請確保數據庫用戶有足夠的權限"
echo "- 定期檢查日誌文件"
echo "- 建議設置 SSL 證書"
echo
echo -e "${GREEN}鋒兄AI資訊系統已準備就緒！🚀${NC}"