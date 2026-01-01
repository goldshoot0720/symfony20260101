@echo off
echo ========================================
echo 鋒兄AI資訊系統 - 本地測試
echo ========================================
echo.

echo 伺服器地址: http://localhost:9000
echo.

echo 測試 API 端點...
echo.

echo 1. API 狀態檢查:
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:9000/api/status' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✓ API 正常運行' -ForegroundColor Green; Write-Host '  版本:' $json.version; Write-Host '  支援模組:' ($json.supported_modules -join ', ') } catch { Write-Host '✗ API 連接失敗' -ForegroundColor Red }"
echo.

echo 2. 食品數據測試:
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:9000/api/foods' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✓ 食品 API 正常' -ForegroundColor Green; Write-Host '  食品總數:' $json.total } catch { Write-Host '✗ 食品 API 失敗' -ForegroundColor Red }"
echo.

echo 3. 訂閱數據測試:
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:9000/api/subscriptions' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✓ 訂閱 API 正常' -ForegroundColor Green; Write-Host '  訂閱總數:' $json.total } catch { Write-Host '✗ 訂閱 API 失敗' -ForegroundColor Red }"
echo.

echo 4. 儀表板統計測試:
powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://localhost:9000/api/dashboard' -UseBasicParsing; $json = $response.Content | ConvertFrom-Json; Write-Host '✓ 儀表板 API 正常' -ForegroundColor Green; Write-Host '  食品總數:' $json.data.foods.total; Write-Host '  訂閱總數:' $json.data.subscriptions.total } catch { Write-Host '✗ 儀表板 API 失敗' -ForegroundColor Red }"
echo.

echo ========================================
echo 測試完成！
echo.
echo 開啟瀏覽器訪問:
echo - 主頁面: http://localhost:9000
echo - 管理介面: http://localhost:9000/manage.html
echo - 測試頁面: http://localhost:9000/test.html
echo ========================================
echo.
echo 按任意鍵繼續...
pause >nul