# 鋒兄AI資訊系統

智能管理您的影片和圖片收藏，支援智能分類和快速搜尋的現代化資訊管理系統。

## 🚀 功能特色

### 核心模組
- **🎬 影片庫管理** - 鋒兄的精美人生與紀實庫存
- **🖼️ 圖片庫管理** - 鋒兄的精美圖片收藏庫 AI 創作 (241 張圖片)
- **🍎 食品管理系統** - 管理您的食品存貨和到期資訊
- **📅 訂閱管理系統** - 管理您的各種訂閱服務和會員資訊
- **📊 系統儀表板** - 即時監控和管理各項資訊

### 技術特色
- 響應式設計，支援各種設備
- 現代化的用戶界面
- RESTful API 架構
- 智能搜索和分類
- 自動到期提醒
- 安全的文件上傳

## 🛠️ 技術架構

### 前端技術
- **SolidJS (SolidStart)** - 現代化前端框架
- **Netlify** - 網頁元件部署
- **Tailwind CSS** - 響應式設計框架
- **Font Awesome** - 圖標庫

### 後端技術
- **PHP** - 服務器端語言
- **MySQL** - 數據庫
- **Strapi CMS** - 內容管理系統
- **RESTful API** - API 架構

### 部署環境
- **Apache** - Web 服務器
- **Hestia Control Panel** - 服務器管理
- **Linux** - 操作系統

## 📁 項目結構

```
feng_ai_system/
├── public_html/                 # 公開網站根目錄
│   ├── index.html              # 主頁面
│   ├── api/                    # API 端點
│   │   └── index.php          # API 路由處理
│   ├── config/                 # 配置文件
│   │   └── database.php       # 數據庫配置
│   ├── uploads/               # 文件上傳目錄
│   ├── storage/               # 存儲目錄
│   ├── .htaccess             # Apache 配置
│   └── robots.txt            # SEO 配置
├── logs/                      # 日誌文件
├── stats/                     # 統計分析
├── private/                   # 私有文件
└── README.md                  # 項目說明
```

## 🚀 快速開始

### 環境要求
- PHP 7.4 或更高版本
- MySQL 5.7 或更高版本
- Apache Web 服務器
- 支援 mod_rewrite

### 安裝步驟

1. **克隆項目**
   ```bash
   git clone https://github.com/your-username/feng-ai-system.git
   cd feng-ai-system
   ```

2. **配置環境**
   ```bash
   cp public_html/.env.example public_html/.env
   # 編輯 .env 文件，設置數據庫連接等配置
   ```

3. **初始化數據庫**
   ```bash
   # 訪問以下 URL 來創建數據庫表
   https://your-domain.com/config/database.php
   ```

4. **設置文件權限**
   ```bash
   chmod 755 public_html/
   chmod 777 public_html/uploads/
   chmod 777 public_html/storage/
   chmod 777 logs/
   ```

5. **配置 Apache**
   確保啟用了 mod_rewrite 模組，並且 .htaccess 文件生效。

## 📊 API 端點

### 系統狀態
- `GET /api/status` - 獲取系統狀態

### 影片管理
- `GET /api/videos` - 獲取影片列表
- `POST /api/videos` - 上傳新影片

### 圖片管理
- `GET /api/images` - 獲取圖片列表
- `POST /api/images` - 上傳新圖片

### 食品管理
- `GET /api/foods` - 獲取食品列表
- `POST /api/foods` - 添加新食品

### 訂閱管理
- `GET /api/subscriptions` - 獲取訂閱列表
- `POST /api/subscriptions` - 添加新訂閱

## 🗄️ 數據庫結構

### 主要數據表
- `users` - 用戶表
- `videos` - 影片表
- `images` - 圖片表
- `foods` - 食品表
- `subscriptions` - 訂閱表
- `payments` - 支付記錄表

## 🔧 配置說明

### 環境變量
詳見 `.env.example` 文件中的配置選項。

### 文件上傳
- 支援的圖片格式：JPG, JPEG, PNG, GIF, WebP
- 支援的影片格式：MP4, AVI, MOV, WMV, FLV
- 最大文件大小：50MB（可配置）

### 安全設置
- CORS 跨域支援
- 文件類型驗證
- SQL 注入防護
- XSS 攻擊防護

## 📱 功能截圖

### 系統首頁
- 現代化的用戶界面
- 功能模組快速訪問
- 技術架構展示

### 儀表板
- 實時統計數據
- 到期提醒
- 快速操作

### 管理模組
- 影片庫管理
- 圖片庫管理
- 食品管理
- 訂閱管理

## 🔄 更新日誌

### v1.0.0 (2025-01-01)
- 初始版本發布
- 基礎功能模組實現
- API 架構建立
- 數據庫結構設計

## 🤝 貢獻指南

歡迎提交 Issue 和 Pull Request 來改進這個項目。

### 開發流程
1. Fork 項目
2. 創建功能分支
3. 提交更改
4. 推送到分支
5. 創建 Pull Request

## 📄 許可證

本項目採用 MIT 許可證。詳見 [LICENSE](LICENSE) 文件。

## 📞 聯繫方式

- **項目作者**: 鋒兄
- **網站**: https://symfony.tpe12thmayor2025to2038.com
- **版權**: 鋒兄達智公開資訊 © 2025 - 2125

## 🙏 致謝

感謝所有為這個項目做出貢獻的開發者和用戶。

---

**鋒兄AI資訊系統** - 智能管理您的數字生活 🚀