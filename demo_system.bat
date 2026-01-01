@echo off
chcp 65001 >nul
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                    🎯 鋒兄AI資訊系統                        ║
echo ║                      完整功能演示                            ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo 🚀 歡迎使用鋒兄AI資訊系統！
echo.
echo 📋 系統功能概覽:
echo   • 🖼️ 圖片庫管理 - 鋒兄的精美圖片收藏庫 AI 創作 (241 張圖片)
echo   • 🎬 影片庫管理 - 鋒兄的精美人生與紀實庫存
echo   • 🍎 食品管理系統 - 管理食品存貨和到期提醒
echo   • 📅 訂閱管理系統 - 管理各種訂閱服務和會員資訊
echo   • 📊 系統儀表板 - 即時監控和數據統計
echo.

echo 🔧 技術架構:
echo   • 前端: 現代化響應式設計 + JavaScript
echo   • 後端: PHP + MySQL + RESTful API
echo   • 部署: Apache Web Server + Hestia CP
echo.

echo 📡 正在檢查系統狀態...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/' -UseBasicParsing; if($response.StatusCode -eq 200) { Write-Host '✅ Web 服務器正常運行' -ForegroundColor Green } else { Write-Host '❌ Web 服務器錯誤' -ForegroundColor Red; exit } } catch { Write-Host '❌ 服務器未啟動，請先運行 start_server.bat' -ForegroundColor Red; pause; exit }"

echo.
echo 🗄️ 正在檢查數據庫連接...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/config/init_database.php' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; if($json.status -eq 'success') { Write-Host '✅ 數據庫連接正常' -ForegroundColor Green; Write-Host '📊 數據庫: feng_symfony' -ForegroundColor Cyan; Write-Host '👤 用戶: root' -ForegroundColor Yellow } else { Write-Host '❌ 數據庫連接失敗' -ForegroundColor Red } } catch { Write-Host '❌ 數據庫測試失敗' -ForegroundColor Red }"

echo.
echo 📈 正在獲取系統統計...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/dashboard' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; if($json.status -eq 'success') { Write-Host '✅ 系統統計數據:' -ForegroundColor Green; Write-Host '  📅 訂閱總數:' $json.data.subscriptions.total -ForegroundColor Cyan; Write-Host '  🍎 食品總數:' $json.data.foods.total -ForegroundColor Yellow; Write-Host '  🖼️ 圖片總數:' $json.data.images.total -ForegroundColor Magenta; Write-Host '  🎬 影片總數:' $json.data.videos.total -ForegroundColor Green } } catch { Write-Host '⚠️ 統計數據獲取失敗' -ForegroundColor Yellow }"

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                        🌐 訪問地址                          ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
echo 🏠 主系統界面:     http://127.0.0.1:9000/
echo 🧪 系統測試頁面:   http://127.0.0.1:9000/test.html
echo 📊 數據庫初始化:   http://127.0.0.1:9000/config/init_database.php
echo.
echo 🔌 API 端點:
echo   • 系統狀態:       http://127.0.0.1:9000/api/status
echo   • 儀表板數據:     http://127.0.0.1:9000/api/dashboard
echo   • 圖片庫:         http://127.0.0.1:9000/api/images
echo   • 影片庫:         http://127.0.0.1:9000/api/videos
echo   • 食品管理:       http://127.0.0.1:9000/api/foods
echo   • 訂閱管理:       http://127.0.0.1:9000/api/subscriptions
echo.

echo ╔══════════════════════════════════════════════════════════════╗
echo ║                        ⌨️ 快捷鍵                            ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
echo   • Ctrl+1: 首頁
echo   • Ctrl+2: 儀表板
echo   • Ctrl+3: 圖片庫
echo   • Ctrl+4: 影片庫
echo   • Ctrl+5: 訂閱管理
echo   • Ctrl+6: 食品管理
echo.

echo ╔══════════════════════════════════════════════════════════════╗
echo ║                        🛠️ 管理工具                          ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
echo   • start_server.bat     - 啟動開發服務器
echo   • test_complete.bat    - 完整功能測試
echo   • test_database.bat    - 數據庫連接測試
echo   • switch_env.bat       - 環境配置切換
echo   • demo_system.bat      - 系統功能演示 (當前)
echo.

echo 🎯 選擇操作:
echo.
echo 1. 🌐 在瀏覽器中打開主系統
echo 2. 🧪 在瀏覽器中打開測試頁面
echo 3. 📊 運行完整功能測試
echo 4. 🔄 切換環境配置
echo 5. 📖 查看使用說明
echo 6. 🚪 退出
echo.

set /p choice=請選擇操作 (1-6): 

if "%choice%"=="1" (
    echo.
    echo 🌐 正在打開主系統界面...
    start http://127.0.0.1:9000/
    echo ✅ 已在瀏覽器中打開主系統
    goto end
)

if "%choice%"=="2" (
    echo.
    echo 🧪 正在打開測試頁面...
    start http://127.0.0.1:9000/test.html
    echo ✅ 已在瀏覽器中打開測試頁面
    goto end
)

if "%choice%"=="3" (
    echo.
    echo 📊 正在運行完整功能測試...
    call test_complete.bat
    goto end
)

if "%choice%"=="4" (
    echo.
    echo 🔄 正在打開環境配置切換...
    call switch_env.bat
    goto end
)

if "%choice%"=="5" (
    echo.
    echo 📖 使用說明:
    echo.
    echo 🎯 基本操作:
    echo   1. 確保服務器正在運行 (運行 start_server.bat)
    echo   2. 在瀏覽器中訪問 http://127.0.0.1:9000/
    echo   3. 使用左側導航切換不同功能模組
    echo   4. 使用搜索功能快速查找內容
    echo.
    echo 🔧 開發功能:
    echo   • 所有數據通過 RESTful API 提供
    echo   • 支援本地測試和遠端部署
    echo   • 自動數據庫初始化和示例數據
    echo   • 響應式設計支援各種設備
    echo.
    echo 💡 提示:
    echo   • 使用 Ctrl+數字鍵快速切換頁面
    echo   • 系統支援實時搜索和過濾
    echo   • 所有操作都有視覺反饋
    echo.
    goto end
)

if "%choice%"=="6" (
    echo.
    echo 👋 感謝使用鋒兄AI資訊系統！
    echo.
    goto exit
)

echo.
echo ❌ 無效選項，請重新選擇
goto choice

:end
echo.
echo 💡 提示: 系統將繼續在後台運行
echo 📞 如需幫助，請查看 README.md 或 INSTALL.md
echo.
pause

:exit