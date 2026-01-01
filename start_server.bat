@echo off
chcp 65001 >nul
echo 🚀 啟動鋒兄AI資訊系統本地服務器...
echo.

cd public_html

echo 📡 正在啟動 PHP 開發服務器...
echo 服務器地址: http://127.0.0.1:9000
echo.
echo 💡 提示: 
echo - 按 Ctrl+C 停止服務器
echo - 在瀏覽器中訪問 http://127.0.0.1:9000 查看系統
echo - 訪問 http://127.0.0.1:9000/test.html 進行系統測試
echo.

php -S 127.0.0.1:9000