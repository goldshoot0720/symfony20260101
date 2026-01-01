# 鋒兄AI資訊系統 - 安裝指南

## 📋 系統要求

### 服務器環境
- **操作系統**: Linux/Windows
- **Web 服務器**: Apache 2.4+ 或 Nginx 1.18+
- **PHP**: 7.4 或更高版本
- **數據庫**: MySQL 5.7+ 或 MariaDB 10.3+
- **存儲空間**: 至少 1GB 可用空間

### PHP 擴展要求
- `php-mysql` (PDO MySQL)
- `php-gd` (圖片處理)
- `php-json` (JSON 支援)
- `php-mbstring` (多字節字符串)
- `php-curl` (HTTP 請求)
- `php-zip` (壓縮文件)

## 🚀 快速安裝

### 方法一：自動部署 (推薦)

#### Windows 系統
```batch
# 運行部署腳本
deploy.bat
```

#### Linux/macOS 系統
```bash
# 給予執行權限
chmod +x deploy.sh

# 運行部署腳本
./deploy.sh
```

### 方法二：手動安裝

#### 1. 下載項目文件
```bash
# 如果使用 Git
git clone https://github.com/your-username/feng-ai-system.git
cd feng-ai-system

# 或者直接下載並解壓縮
```

#### 2. 配置環境變量
```bash
# 複製環境配置文件
cp public_html/.env.example public_html/.env

# 編輯配置文件
nano public_html/.env
```

#### 3. 設置目錄權限
```bash
# Linux/macOS
chmod 755 public_html/
chmod 777 public_html/uploads/
chmod 777 public_html/storage/
chmod 777 logs/

# Windows (通過文件屬性設置)
```

#### 4. 配置 Web 服務器

##### Apache 配置
確保啟用以下模組：
```apache
LoadModule rewrite_module modules/mod_rewrite.so
LoadModule headers_module modules/mod_headers.so
```

虛擬主機配置示例：
```apache
<VirtualHost *:80>
    ServerName symfony.tpe12thmayor2025to2038.com
    DocumentRoot /path/to/feng-ai-system/public_html
    
    <Directory /path/to/feng-ai-system/public_html>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog logs/feng_ai_error.log
    CustomLog logs/feng_ai_access.log combined
</VirtualHost>
```

##### Nginx 配置
```nginx
server {
    listen 80;
    server_name symfony.tpe12thmayor2025to2038.com;
    root /path/to/feng-ai-system/public_html;
    index index.html index.php;

    location / {
        try_files $uri $uri/ /index.html;
    }

    location /api {
        try_files $uri $uri/ /api/index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

#### 5. 創建數據庫
```sql
-- 登入 MySQL
mysql -u root -p

-- 創建數據庫
CREATE DATABASE feng_ai_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 創建用戶（可選）
CREATE USER 'feng_user'@'localhost' IDENTIFIED BY 'your_password';
GRANT ALL PRIVILEGES ON feng_ai_system.* TO 'feng_user'@'localhost';
FLUSH PRIVILEGES;
```

#### 6. 初始化數據庫表
訪問以下 URL 或運行 PHP 腳本：
```
http://your-domain.com/config/database.php
```

或者：
```bash
php public_html/config/database.php
```

## 🔧 配置說明

### 環境變量配置 (.env)
```env
# 應用程序設置
APP_NAME="鋒兄AI資訊系統"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://symfony.tpe12thmayor2025to2038.com

# 數據庫配置
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=feng_ai_system
DB_USERNAME=feng_user
DB_PASSWORD=your_password

# 文件上傳設置
UPLOAD_MAX_SIZE=50M
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp
ALLOWED_VIDEO_TYPES=mp4,avi,mov,wmv,flv
```

### PHP 配置調整
編輯 `php.ini` 文件：
```ini
# 文件上傳限制
upload_max_filesize = 50M
post_max_size = 50M
max_execution_time = 300
memory_limit = 256M

# 時區設置
date.timezone = Asia/Taipei
```

## 🧪 測試安裝

### 1. 訪問測試頁面
```
http://your-domain.com/test.html
```

### 2. 測試 API 端點
```bash
# 測試系統狀態
curl http://your-domain.com/api/status

# 測試數據獲取
curl http://your-domain.com/api/foods
curl http://your-domain.com/api/subscriptions
```

### 3. 檢查日誌
```bash
# 查看錯誤日誌
tail -f logs/*.log

# 查看 Apache 錯誤日誌
tail -f /var/log/apache2/error.log
```

## 🔒 安全設置

### 1. 設置 SSL 證書
```bash
# 使用 Let's Encrypt (推薦)
certbot --apache -d symfony.tpe12thmayor2025to2038.com
```

### 2. 防火牆配置
```bash
# Ubuntu/Debian
ufw allow 80
ufw allow 443
ufw enable

# CentOS/RHEL
firewall-cmd --permanent --add-service=http
firewall-cmd --permanent --add-service=https
firewall-cmd --reload
```

### 3. 文件權限安全
```bash
# 限制敏感文件訪問
chmod 600 public_html/.env
chmod 600 public_html/config/database.php
```

## 📊 性能優化

### 1. 啟用 OPcache
在 `php.ini` 中：
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
```

### 2. 配置緩存
```bash
# 創建緩存目錄
mkdir -p public_html/storage/cache
chmod 777 public_html/storage/cache
```

### 3. 數據庫優化
```sql
-- 為常用查詢添加索引
ALTER TABLE foods ADD INDEX idx_expiry_date (expiry_date);
ALTER TABLE subscriptions ADD INDEX idx_next_payment (next_payment_date);
```

## 🔄 維護任務

### 1. 設置定時任務
```bash
# 編輯 crontab
crontab -e

# 添加以下任務
# 每日備份
0 2 * * * /path/to/feng-ai-system/backup.sh

# 每小時監控
0 * * * * /path/to/feng-ai-system/monitor.sh

# 清理臨時文件
0 3 * * * find /path/to/feng-ai-system/public_html/storage/temp -type f -mtime +1 -delete
```

### 2. 日誌輪轉
```bash
# 安裝 logrotate 配置
sudo cp logrotate.conf /etc/logrotate.d/feng-ai-system
```

## ❗ 常見問題

### Q: API 返回 404 錯誤
**A**: 檢查 Apache mod_rewrite 是否啟用，確保 .htaccess 文件存在且可讀。

### Q: 數據庫連接失敗
**A**: 檢查 .env 文件中的數據庫配置，確保數據庫服務正在運行。

### Q: 文件上傳失敗
**A**: 檢查上傳目錄權限，確保 PHP 有寫入權限。

### Q: 頁面顯示空白
**A**: 檢查 PHP 錯誤日誌，可能是語法錯誤或缺少擴展。

### Q: CSS/JS 文件無法載入
**A**: 檢查文件路徑和 Web 服務器配置，確保靜態文件可以正常訪問。

## 📞 技術支援

如果遇到安裝問題，請：

1. 檢查系統要求是否滿足
2. 查看錯誤日誌文件
3. 訪問測試頁面診斷問題
4. 參考常見問題解答

## 🎉 安裝完成

安裝成功後，您可以：

1. 訪問主頁面開始使用系統
2. 通過 API 集成其他應用
3. 自定義配置和主題
4. 設置定期備份和監控

**恭喜！鋒兄AI資訊系統已成功安裝！** 🚀