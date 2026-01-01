@echo off
chcp 65001 >nul
echo 🚀 開始部署鋒兄AI資訊系統...
echo.

REM 檢查 PHP 是否安裝
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ PHP 未安裝或未添加到 PATH
    pause
    exit /b 1
)

echo ✅ PHP 已安裝

REM 創建必要的目錄
echo 📁 創建目錄結構...
if not exist "public_html\uploads\images" mkdir "public_html\uploads\images"
if not exist "public_html\uploads\videos" mkdir "public_html\uploads\videos"
if not exist "public_html\uploads\thumbnails" mkdir "public_html\uploads\thumbnails"
if not exist "public_html\storage\temp" mkdir "public_html\storage\temp"
if not exist "public_html\storage\cache" mkdir "public_html\storage\cache"
if not exist "logs" mkdir "logs"

echo ✅ 目錄創建完成

REM 檢查 .env 文件
if not exist "public_html\.env" (
    echo ⚠️ 未找到 .env 文件，複製示例文件...
    copy "public_html\.env.example" "public_html\.env"
    echo ⚠️ 請編輯 public_html\.env 文件並配置數據庫連接
)

REM 測試 API
echo 🔍 測試系統...
echo 請在瀏覽器中訪問以下 URL 來測試系統：
echo.
echo 主頁面: http://localhost/
echo API 狀態: http://localhost/api/status
echo 數據庫初始化: http://localhost/config/database.php
echo.

echo ✅ 部署完成！
echo.
echo 📋 接下來的步驟：
echo 1. 編輯 public_html\.env 文件配置數據庫連接
echo 2. 確保 Web 服務器（Apache/Nginx）正在運行
echo 3. 訪問 http://localhost/config/database.php 初始化數據庫
echo 4. 訪問 http://localhost/ 測試系統
echo.
echo 🎉 鋒兄AI資訊系統已準備就緒！
pause