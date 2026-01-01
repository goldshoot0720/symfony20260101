@echo off
chcp 65001 >nul
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                    🚀 鋒兄AI資訊系統                        ║
echo ║                      完整功能測試                            ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo 📡 測試系統狀態...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/' -UseBasicParsing; if($response.StatusCode -eq 200) { Write-Host '✅ 主頁面正常載入' -ForegroundColor Green } else { Write-Host '❌ 主頁面錯誤' -ForegroundColor Red } } catch { Write-Host '❌ 服務器未啟動，請先運行 start_server.bat' -ForegroundColor Red; exit }"

echo.
echo 🔌 測試 API 端點...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/status' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ API 狀態:' $json.status -ForegroundColor Green; Write-Host '📝 系統:' $json.message -ForegroundColor Cyan; Write-Host '🔢 版本:' $json.version -ForegroundColor Yellow } catch { Write-Host '❌ API 連接失敗' -ForegroundColor Red }"

echo.
echo 📊 測試儀表板數據...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/dashboard' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ 儀表板數據正常' -ForegroundColor Green; Write-Host '📈 訂閱總數:' $json.data.subscriptions.total -ForegroundColor Cyan; Write-Host '🍎 食品總數:' $json.data.foods.total -ForegroundColor Yellow } catch { Write-Host '⚠️ 儀表板數據獲取失敗' -ForegroundColor Yellow }"

echo.
echo 🖼️ 測試圖片庫...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/images' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ 圖片庫數據正常' -ForegroundColor Green; Write-Host '📸 圖片總數:' $json.total -ForegroundColor Cyan; Write-Host '💾 總大小:' $json.summary.total_size -ForegroundColor Yellow } catch { Write-Host '⚠️ 圖片庫數據獲取失敗' -ForegroundColor Yellow }"

echo.
echo 🎬 測試影片庫...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/videos' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ 影片庫數據正常' -ForegroundColor Green; Write-Host '🎥 影片總數:' $json.total -ForegroundColor Cyan } catch { Write-Host '⚠️ 影片庫數據獲取失敗' -ForegroundColor Yellow }"

echo.
echo 🍎 測試食品管理...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/foods' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ 食品管理數據正常' -ForegroundColor Green; Write-Host '🥘 食品總數:' $json.total -ForegroundColor Cyan } catch { Write-Host '⚠️ 食品管理數據獲取失敗' -ForegroundColor Yellow }"

echo.
echo 📅 測試訂閱管理...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/subscriptions' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ 訂閱管理數據正常' -ForegroundColor Green; Write-Host '📋 訂閱總數:' $json.total -ForegroundColor Cyan } catch { Write-Host '⚠️ 訂閱管理數據獲取失敗' -ForegroundColor Yellow }"

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                        🌐 測試 URL                          ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
echo 🏠 主系統界面:     http://127.0.0.1:9000/
echo 🧪 系統測試頁面:   http://127.0.0.1:9000/test.html
echo 📊 API 狀態:       http://127.0.0.1:9000/api/status
echo 📈 儀表板數據:     http://127.0.0.1:9000/api/dashboard
echo 🖼️ 圖片庫 API:     http://127.0.0.1:9000/api/images
echo 🎬 影片庫 API:     http://127.0.0.1:9000/api/videos
echo 🍎 食品管理 API:   http://127.0.0.1:9000/api/foods
echo 📅 訂閱管理 API:   http://127.0.0.1:9000/api/subscriptions
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                        💡 使用提示                          ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
echo 🎯 功能特色:
echo   • 響應式設計，支援各種設備
echo   • 智能搜索和分類功能
echo   • 自動到期提醒系統
echo   • RESTful API 架構
echo   • 現代化用戶界面
echo.
echo ⌨️ 快捷鍵:
echo   • Ctrl+1: 首頁
echo   • Ctrl+2: 儀表板
echo   • Ctrl+3: 圖片庫
echo   • Ctrl+4: 影片庫
echo   • Ctrl+5: 訂閱管理
echo   • Ctrl+6: 食品管理
echo.
echo 🚀 系統已完全準備就緒！請在瀏覽器中體驗完整功能。
echo.
pause