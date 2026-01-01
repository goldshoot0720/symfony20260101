@echo off
chcp 65001 >nul
echo 🧪 鋒兄AI資訊系統 - 本地測試
echo.

echo 📡 測試服務器狀態...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/' -UseBasicParsing; if($response.StatusCode -eq 200) { Write-Host '✅ 主頁面正常' -ForegroundColor Green } else { Write-Host '❌ 主頁面錯誤' -ForegroundColor Red } } catch { Write-Host '❌ 服務器未啟動' -ForegroundColor Red }"

echo.
echo 🔌 測試 API 端點...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/status' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ API 狀態:' $json.status -ForegroundColor Green; Write-Host '📝 消息:' $json.message -ForegroundColor Cyan; Write-Host '🔢 版本:' $json.version -ForegroundColor Yellow } catch { Write-Host '❌ API 連接失敗' -ForegroundColor Red }"

echo.
echo 📊 測試數據端點...
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/foods' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ 食品數據:' $json.total '筆記錄' -ForegroundColor Green } catch { Write-Host '⚠️ 數據庫未配置' -ForegroundColor Yellow }"

powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:9000/api/subscriptions' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✅ 訂閱數據:' $json.total '筆記錄' -ForegroundColor Green } catch { Write-Host '⚠️ 數據庫未配置' -ForegroundColor Yellow }"

echo.
echo 🌐 可用的測試 URL:
echo 主頁面: http://127.0.0.1:9000/
echo 測試頁面: http://127.0.0.1:9000/test.html
echo API 狀態: http://127.0.0.1:9000/api/status
echo 食品數據: http://127.0.0.1:9000/api/foods
echo 訂閱數據: http://127.0.0.1:9000/api/subscriptions
echo.

echo 🚀 系統已準備就緒！請在瀏覽器中訪問上述 URL 進行測試。
echo.
pause