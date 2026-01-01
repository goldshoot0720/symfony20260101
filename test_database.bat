@echo off
chcp 65001 >nul
echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                    🗄️ 數據庫連接測試                        ║
echo ║                   鋒兄AI資訊系統                             ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.

echo 📡 檢查服務器狀態...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/' -UseBasicParsing; if($response.StatusCode -eq 200) { Write-Host '✅ 服務器正常運行' -ForegroundColor Green } else { Write-Host '❌ 服務器錯誤' -ForegroundColor Red } } catch { Write-Host '❌ 服務器未啟動，請先運行 start_server.bat' -ForegroundColor Red; pause; exit }"

echo.
echo 🔌 測試數據庫連接和初始化...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/config/init_database.php' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; if($json.status -eq 'success') { Write-Host '✅ 數據庫連接成功' -ForegroundColor Green; Write-Host '📊 數據庫:' $json.connection.database -ForegroundColor Cyan; Write-Host '👤 用戶:' $json.connection.username -ForegroundColor Yellow; Write-Host '🏠 主機:' $json.connection.host -ForegroundColor Magenta; Write-Host '📋 創建表:' ($json.tables_created -join ', ') -ForegroundColor Green } else { Write-Host '❌ 數據庫連接失敗:' $json.message -ForegroundColor Red } } catch { Write-Host '❌ 數據庫測試失敗' -ForegroundColor Red }"

echo.
echo 📊 測試數據 API...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/dashboard' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; if($json.status -eq 'success') { Write-Host '✅ 儀表板數據正常' -ForegroundColor Green; Write-Host '📈 訂閱總數:' $json.data.subscriptions.total -ForegroundColor Cyan; Write-Host '🍎 食品總數:' $json.data.foods.total -ForegroundColor Yellow; Write-Host '🖼️ 圖片總數:' $json.data.images.total -ForegroundColor Magenta; Write-Host '🎬 影片總數:' $json.data.videos.total -ForegroundColor Green } else { Write-Host '❌ 數據獲取失敗' -ForegroundColor Red } } catch { Write-Host '❌ API 測試失敗' -ForegroundColor Red }"

echo.
echo 🍎 測試食品數據...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/foods' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; if($json.status -eq 'success') { Write-Host '✅ 食品數據正常，共' $json.total '項' -ForegroundColor Green; foreach($food in $json.data) { Write-Host '  •' $food.name '(剩餘' $food.days_remaining '天)' -ForegroundColor Cyan } } else { Write-Host '❌ 食品數據獲取失敗' -ForegroundColor Red } } catch { Write-Host '❌ 食品API測試失敗' -ForegroundColor Red }"

echo.
echo 📅 測試訂閱數據...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/subscriptions' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; if($json.status -eq 'success') { Write-Host '✅ 訂閱數據正常，共' $json.total '項' -ForegroundColor Green; foreach($sub in $json.data) { Write-Host '  •' $sub.name '(剩餘' $sub.days_remaining '天)' -ForegroundColor Cyan } } else { Write-Host '❌ 訂閱數據獲取失敗' -ForegroundColor Red } } catch { Write-Host '❌ 訂閱API測試失敗' -ForegroundColor Red }"

echo.
echo ╔══════════════════════════════════════════════════════════════╗
echo ║                        📋 環境信息                          ║
echo ╚══════════════════════════════════════════════════════════════╝
echo.
echo 🔧 本地測試環境:
echo   • 數據庫: feng_symfony
echo   • 用戶: root
echo   • 密碼: (空白)
echo   • 主機: localhost
echo.
echo 🌐 遠端上線環境:
echo   • 數據庫: feng_symfony  
echo   • 用戶: feng_laravel
echo   • 密碼: ym0Tagood129
echo   • 主機: localhost
echo.
echo 💡 提示: 
echo   • 本地測試使用 root 用戶和空密碼
echo   • 上線時會自動切換到遠端配置
echo   • 數據庫表會自動創建和初始化
echo.
echo 🚀 數據庫測試完成！
echo.
pause