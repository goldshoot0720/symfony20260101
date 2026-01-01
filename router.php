<?php
// PHP 內建伺服器路由檔案

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// 移除查詢參數
$path = strtok($path, '?');

// API 路由
if (strpos($path, '/api/') === 0) {
    // 重寫 API 路由到 api/index.php
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/api/index.php';
    require_once __DIR__ . '/public_html/api/index.php';
    return true;
}

// 靜態檔案檢查
$file = __DIR__ . '/public_html' . $path;

// 如果是目錄，嘗試 index.html
if (is_dir($file)) {
    $file .= '/index.html';
    $path .= '/index.html';
}

// 如果檔案存在，讓 PHP 內建伺服器處理
if (file_exists($file)) {
    return false;
}

// 其他所有請求都重定向到 welcome.html (SPA 路由)
if ($path === '/' || $path === '/index.html') {
    require_once __DIR__ . '/public_html/welcome.html';
    return true;
}

return false;
?>