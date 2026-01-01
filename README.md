# 鋒兄AI資訊系統

一個專為管理食品存貨和訂閱服務而設計的智能資訊管理系統。

## 🚀 系統特色

- **🍎 食品管理** - 智能追蹤食品存貨、到期日提醒
- **📅 訂閱管理** - 管理各種訂閱服務和付款提醒  
- **📊 智能儀表板** - 即時統計和狀態監控
- **🎨 響應式設計** - 支援桌面和行動裝置
- **⚡ 高效能API** - RESTful API 架構

## 📋 系統需求

- PHP 8.0+
- MySQL 5.7+
- Apache/Nginx Web Server
- 現代瀏覽器支援

## 🛠️ 安裝說明

### 1. 環境配置

```bash
# 複製環境配置檔案
cp public_html/.env.example public_html/.env

# 編輯數據庫配置
# 本地測試: localhost/root/空白密碼
# 遠端部署: localhost/feng_laravel/ym0Tagood129
```

### 2. 數據庫設置

```bash
# 創建數據庫
CREATE DATABASE feng_symfony;

# 執行數據庫初始化
php public_html/config/create_required_tables.php
```

### 3. 啟動系統

```bash
# 本地開發伺服器
php -S localhost:8080 -t public_html

# 或使用 Apache/Nginx 指向 public_html 目錄
```

## 📁 專案結構

```
feng_symfony/
├── public_html/           # Web 根目錄
│   ├── index.html        # 主頁面
│   ├── manage.html       # 管理介面
│   ├── test.html         # 系統測試
│   ├── api/              # API 端點
│   │   └── index.php     # RESTful API
│   ├── config/           # 配置檔案
│   │   ├── database.php  # 數據庫配置
│   │   └── *.php         # 其他配置
│   └── .env              # 環境變數
├── logs/                 # 系統日誌
└── README.md            # 說明文件
```

## 🔧 API 端點

### 食品管理
- `GET /api/foods` - 獲取食品列表
- `POST /api/foods` - 新增食品

### 訂閱管理  
- `GET /api/subscriptions` - 獲取訂閱列表
- `POST /api/subscriptions` - 新增訂閱

### 系統狀態
- `GET /api/status` - API 狀態檢查
- `GET /api/dashboard` - 儀表板統計

## 🗄️ 資料庫結構

### food 表
```sql
CREATE TABLE `food` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text,
  `todate` date DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `photo` text,
  `price` int DEFAULT NULL,
  `shop` text,
  `photohash` text,
  PRIMARY KEY (`id`)
);
```

### subscription 表
```sql
CREATE TABLE `subscription` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` text,
  `nextdate` date DEFAULT NULL,
  `price` int DEFAULT NULL,
  `site` text,
  `note` text,
  `account` text,
  PRIMARY KEY (`id`)
);
```

## 🧪 測試

```bash
# 檢查數據庫連接
php public_html/config/database.php

# 系統測試頁面
http://localhost:8080/test.html

# 管理介面
http://localhost:8080/manage.html
```

## 🚀 部署

### 本地測試
```bash
# 使用內建伺服器
php -S localhost:8080 -t public_html
```

### 生產環境
1. 上傳檔案到伺服器
2. 設定 Web 伺服器指向 `public_html` 目錄
3. 配置數據庫連接
4. 執行數據庫初始化

## 📊 功能特色

- ✅ 食品到期提醒 (3天、7天、30天)
- ✅ 訂閱付款提醒 (3天、7天內到期)
- ✅ 響應式管理介面
- ✅ RESTful API 架構
- ✅ 數據庫自動檢查和修復
- ✅ 環境配置管理

## 🔒 安全性

- 環境變數配置
- SQL 注入防護 (PDO Prepared Statements)
- CORS 跨域請求控制
- 輸入驗證和清理

## 📝 更新日誌

### v1.0.0 (2025-01-01)
- ✅ 完成食品和訂閱管理核心功能
- ✅ 實現響應式管理介面
- ✅ 建立 RESTful API 架構
- ✅ 數據庫結構優化 (僅保留必要的兩個表)
- ✅ 系統測試和部署工具

## 🤝 貢獻

歡迎提交 Issue 和 Pull Request 來改善系統功能。

## 📄 授權

MIT License - 詳見 [LICENSE](LICENSE) 檔案

---

**鋒兄達智公開資訊 © 版權所有 2025 - 2125**