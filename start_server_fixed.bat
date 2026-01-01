@echo off
echo ========================================
echo 鋒兄AI資訊系統 - 啟動伺服器
echo ========================================
echo.

echo 檢查 PHP 版本...
php --version
echo.

echo 檢查數據庫連接...
php public_html/config/database.php
echo.

echo 啟動 PHP 開發伺服器...
echo 伺服器地址: http://localhost:9000
echo 按 Ctrl+C 停止伺服器
echo.

php -S localhost:9000 -t public_html router.php