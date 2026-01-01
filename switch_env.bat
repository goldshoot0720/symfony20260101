@echo off
chcp 65001 >nul
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                    🔄 環境配置切換                          ║
echo ║                   鋒兄AI資訊系統                             ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo 請選擇要切換的環境:
echo.
echo 1. 本地測試環境 (localhost, root, 空密碼)
echo 2. 遠端上線環境 (localhost, feng_laravel, ym0Tagood129)
echo 3. 查看當前環境配置
echo 4. 退出
echo.

set /p choice=請輸入選項 (1-4): 

if "%choice%"=="1" goto local
if "%choice%"=="2" goto remote
if "%choice%"=="3" goto show
if "%choice%"=="4" goto exit
goto invalid

:local
echo.
echo 🔄 切換到本地測試環境...
(
echo # 鋒兄AI資訊系統環境配置 - 本地測試
echo.
echo # 應用程序設置
echo APP_NAME="鋒兄AI資訊系統"
echo APP_ENV=development
echo APP_DEBUG=true
echo APP_URL=http://127.0.0.1:9000
echo.
echo # 數據庫配置 - 本地測試
echo DB_HOST=localhost
echo DB_PORT=3306
echo DB_DATABASE=feng_symfony
echo DB_USERNAME=root
echo DB_PASSWORD=
echo.
echo # 文件上傳設置
echo UPLOAD_MAX_SIZE=50M
echo ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp
echo ALLOWED_VIDEO_TYPES=mp4,avi,mov,wmv,flv
echo.
echo # 存儲路徑
echo STORAGE_PATH=storage
echo UPLOADS_PATH=uploads
echo THUMBNAILS_PATH=thumbnails
echo.
echo # API 設置
echo API_VERSION=v1
echo API_RATE_LIMIT=100
echo.
echo # 系統設置
echo TIMEZONE=Asia/Taipei
echo LOCALE=zh_TW
echo DEFAULT_LANGUAGE=zh-TW
echo.
echo # 本地測試模式
echo LOCAL_TEST=true
) > public_html\.env

echo ✅ 已切換到本地測試環境
echo 📊 數據庫: feng_symfony
echo 👤 用戶: root
echo 🔑 密碼: (空白)
goto end

:remote
echo.
echo 🔄 切換到遠端上線環境...
(
echo # 鋒兄AI資訊系統環境配置 - 遠端上線
echo.
echo # 應用程序設置
echo APP_NAME="鋒兄AI資訊系統"
echo APP_ENV=production
echo APP_DEBUG=false
echo APP_URL=https://symfony.tpe12thmayor2025to2038.com
echo.
echo # 數據庫配置 - 遠端上線
echo DB_HOST=localhost
echo DB_PORT=3306
echo DB_DATABASE=feng_symfony
echo DB_USERNAME=feng_laravel
echo DB_PASSWORD=ym0Tagood129
echo.
echo # 文件上傳設置
echo UPLOAD_MAX_SIZE=50M
echo ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp
echo ALLOWED_VIDEO_TYPES=mp4,avi,mov,wmv,flv
echo.
echo # 存儲路徑
echo STORAGE_PATH=storage
echo UPLOADS_PATH=uploads
echo THUMBNAILS_PATH=thumbnails
echo.
echo # API 設置
echo API_VERSION=v1
echo API_RATE_LIMIT=100
echo.
echo # 系統設置
echo TIMEZONE=Asia/Taipei
echo LOCALE=zh_TW
echo DEFAULT_LANGUAGE=zh-TW
echo.
echo # 生產環境模式
echo LOCAL_TEST=false
) > public_html\.env

echo ✅ 已切換到遠端上線環境
echo 📊 數據庫: feng_symfony
echo 👤 用戶: feng_laravel
echo 🔑 密碼: ym0Tagood129
goto end

:show
echo.
echo 📋 當前環境配置:
echo.
if exist "public_html\.env" (
    powershell -Command "Get-Content 'public_html\.env' | Where-Object { $_ -match '^(APP_ENV|DB_HOST|DB_DATABASE|DB_USERNAME|DB_PASSWORD)=' } | ForEach-Object { Write-Host $_ -ForegroundColor Cyan }"
) else (
    echo ❌ 未找到 .env 配置文件
)
goto end

:invalid
echo.
echo ❌ 無效選項，請重新選擇
goto start

:end
echo.
echo 💡 提示: 切換環境後請重新啟動服務器以使配置生效
echo.
pause
goto exit

:exit