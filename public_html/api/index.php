<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// 處理 OPTIONS 請求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 獲取數據庫連接
$database = new Database();
$db = $database->getConnection();

// 簡單的路由系統
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$method = $_SERVER['REQUEST_METHOD'];

// 路由配置
$routes = [
    'GET' => [
        '/status' => 'getStatus',
        '/videos' => 'getVideos',
        '/images' => 'getImages',
        '/foods' => 'getFoods',
        '/subscriptions' => 'getSubscriptions',
        '/dashboard' => 'getDashboardStats'
    ],
    'POST' => [
        '/videos' => 'createVideo',
        '/images' => 'createImage',
        '/foods' => 'createFood',
        '/subscriptions' => 'createSubscription'
    ]
];

// 執行路由
if (isset($routes[$method][$path])) {
    $function = $routes[$method][$path];
    if (function_exists($function)) {
        $function();
    } else {
        http_response_code(501);
        echo json_encode(['error' => 'Function not implemented']);
    }
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}

// API 函數
function getStatus() {
    echo json_encode([
        'status' => 'success',
        'message' => '鋒兄AI資訊系統 API 運行中',
        'version' => '1.0.0',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
}

function getVideos() {
    global $db;
    
    try {
        if ($db) {
            $stmt = $db->query("SELECT * FROM videos ORDER BY created_at DESC");
            $videos = $stmt->fetchAll();
        } else {
            // 如果數據庫未連接，使用模擬數據
            $videos = [
                [
                    'id' => 1,
                    'title' => '鋒兄的傳奇人生',
                    'description' => '鋒兄人生紀錄片庫存',
                    'file_size' => 2109440,
                    'duration' => '00:45',
                    'format' => 'MP4',
                    'created_at' => '2025-01-01'
                ],
                [
                    'id' => 2,
                    'title' => '鋒兄雜耍Show 🔥',
                    'description' => '鋒兄精彩表演庫存',
                    'file_size' => 4415488,
                    'duration' => '01:23',
                    'format' => 'MP4',
                    'created_at' => '2025-01-01'
                ]
            ];
        }
        
        // 格式化文件大小
        foreach ($videos as &$video) {
            if (isset($video['file_size'])) {
                $video['size'] = formatFileSize($video['file_size']);
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $videos,
            'total' => count($videos)
        ], JSON_UNESCAPED_UNICODE);
        
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '獲取影片數據失敗: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function getImages() {
    global $db;
    
    try {
        if ($db) {
            $stmt = $db->query("SELECT * FROM images ORDER BY created_at DESC");
            $images = $stmt->fetchAll();
            
            // 獲取統計信息
            $stats_stmt = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(file_size) as total_size,
                    SUM(CASE WHEN format = 'PNG' THEN 1 ELSE 0 END) as png_count,
                    SUM(CASE WHEN format = 'JPG' THEN 1 ELSE 0 END) as jpg_count,
                    SUM(CASE WHEN format = 'JPEG' THEN 1 ELSE 0 END) as jpeg_count
                FROM images
            ");
            $stats = $stats_stmt->fetch();
        } else {
            // 模擬數據
            $images = [
                [
                    'id' => 1,
                    'filename' => '1761405813-e...',
                    'format' => 'JPG',
                    'file_size' => 908288,
                    'full_name' => '1761405813-eha...',
                    'created_at' => '2025-01-01'
                ],
                [
                    'id' => 2,
                    'filename' => '1761405863-3...',
                    'format' => 'JPG',
                    'file_size' => 748544,
                    'full_name' => '1761405863-3ca...',
                    'created_at' => '2025-01-01'
                ]
            ];
            
            $stats = [
                'total' => 241,
                'total_size' => 656505856,
                'png_count' => 192,
                'jpg_count' => 41,
                'jpeg_count' => 8
            ];
        }
        
        // 格式化文件大小
        foreach ($images as &$image) {
            if (isset($image['file_size'])) {
                $image['size'] = formatFileSize($image['file_size']);
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $images,
            'total' => $stats['total'],
            'summary' => [
                'total_size' => formatFileSize($stats['total_size']),
                'png_count' => $stats['png_count'],
                'jpg_count' => $stats['jpg_count'],
                'jpeg_count' => $stats['jpeg_count']
            ]
        ], JSON_UNESCAPED_UNICODE);
        
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '獲取圖片數據失敗: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function getFoods() {
    global $db;
    
    try {
        if ($db) {
            $stmt = $db->query("
                SELECT *, 
                DATEDIFF(expiry_date, CURDATE()) as days_remaining 
                FROM foods 
                ORDER BY expiry_date ASC
            ");
            $foods = $stmt->fetchAll();
        } else {
            // 模擬數據
            $foods = [
                [
                    'id' => 1,
                    'name' => '【張君雅】五香海苔休閒丸子',
                    'quantity' => 3,
                    'price' => 0,
                    'expiry_date' => '2026-01-06',
                    'days_remaining' => 15,
                    'status' => '新鮮'
                ],
                [
                    'id' => 2,
                    'name' => '【張君雅】日式串燒休閒丸子',
                    'quantity' => 6,
                    'price' => 0,
                    'expiry_date' => '2025-01-07',
                    'days_remaining' => 16,
                    'status' => '新鮮'
                ]
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $foods,
            'total' => count($foods)
        ], JSON_UNESCAPED_UNICODE);
        
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '獲取食品數據失敗: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function getSubscriptions() {
    global $db;
    
    try {
        if ($db) {
            $stmt = $db->query("
                SELECT *, 
                DATEDIFF(next_payment_date, CURDATE()) as days_remaining 
                FROM subscriptions 
                ORDER BY next_payment_date ASC
            ");
            $subscriptions = $stmt->fetchAll();
        } else {
            // 模擬數據
            $subscriptions = [
                [
                    'id' => 1,
                    'name' => '天虎/黃信訊/心臟內科',
                    'service_url' => 'https://www.tcmg.com.tw/index.php/main/schedule_time?id=18',
                    'price' => 530,
                    'next_payment_date' => '2025-12-26',
                    'days_remaining' => 25,
                    'status' => '即將到期'
                ],
                [
                    'id' => 2,
                    'name' => 'kiro pro',
                    'service_url' => 'https://app.kiro.dev/account/',
                    'price' => 640,
                    'next_payment_date' => '2026-01-01',
                    'days_remaining' => 10,
                    'status' => '正常'
                ]
            ];
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $subscriptions,
            'total' => count($subscriptions)
        ], JSON_UNESCAPED_UNICODE);
        
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '獲取訂閱數據失敗: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function createVideo() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // 這裡應該處理文件上傳和數據庫插入
    echo json_encode([
        'status' => 'success',
        'message' => '影片上傳功能開發中',
        'data' => $input
    ]);
}

function createImage() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // 這裡應該處理文件上傳和數據庫插入
    echo json_encode([
        'status' => 'success',
        'message' => '圖片上傳功能開發中',
        'data' => $input
    ]);
}

function createFood() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // 這裡應該處理數據庫插入
    echo json_encode([
        'status' => 'success',
        'message' => '食品新增功能開發中',
        'data' => $input
    ]);
}

function createSubscription() {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // 這裡應該處理數據庫插入
    echo json_encode([
        'status' => 'success',
        'message' => '訂閱新增功能開發中',
        'data' => $input
    ]);
}

function getDashboardStats() {
    global $db;
    
    try {
        if ($db) {
            // 獲取訂閱統計
            $sub_stmt = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN DATEDIFF(next_payment_date, CURDATE()) <= 3 THEN 1 ELSE 0 END) as expiring_3_days,
                    SUM(CASE WHEN DATEDIFF(next_payment_date, CURDATE()) <= 7 THEN 1 ELSE 0 END) as expiring_7_days,
                    SUM(CASE WHEN next_payment_date < CURDATE() THEN 1 ELSE 0 END) as expired
                FROM subscriptions
            ");
            $sub_stats = $sub_stmt->fetch();
            
            // 獲取食品統計
            $food_stmt = $db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN DATEDIFF(expiry_date, CURDATE()) <= 3 THEN 1 ELSE 0 END) as expiring_3_days,
                    SUM(CASE WHEN DATEDIFF(expiry_date, CURDATE()) <= 7 THEN 1 ELSE 0 END) as expiring_7_days,
                    SUM(CASE WHEN DATEDIFF(expiry_date, CURDATE()) <= 30 THEN 1 ELSE 0 END) as expiring_30_days,
                    SUM(CASE WHEN expiry_date < CURDATE() THEN 1 ELSE 0 END) as expired
                FROM foods
            ");
            $food_stats = $food_stmt->fetch();
            
            // 獲取圖片統計
            $img_stmt = $db->query("SELECT COUNT(*) as total, SUM(file_size) as total_size FROM images");
            $img_stats = $img_stmt->fetch();
            
            // 獲取影片統計
            $vid_stmt = $db->query("SELECT COUNT(*) as total, SUM(file_size) as total_size FROM videos");
            $vid_stats = $vid_stmt->fetch();
            
        } else {
            // 模擬數據
            $sub_stats = ['total' => 24, 'expiring_3_days' => 0, 'expiring_7_days' => 1, 'expired' => 0];
            $food_stats = ['total' => 13, 'expiring_3_days' => 0, 'expiring_7_days' => 0, 'expiring_30_days' => 2, 'expired' => 0];
            $img_stats = ['total' => 241, 'total_size' => 656505856];
            $vid_stats = ['total' => 2, 'total_size' => 6524928];
        }
        
        $stats = [
            'subscriptions' => [
                'total' => (int)$sub_stats['total'],
                'expiring_3_days' => (int)$sub_stats['expiring_3_days'],
                'expiring_7_days' => (int)$sub_stats['expiring_7_days'],
                'expired' => (int)$sub_stats['expired']
            ],
            'foods' => [
                'total' => (int)$food_stats['total'],
                'expiring_3_days' => (int)$food_stats['expiring_3_days'],
                'expiring_7_days' => (int)$food_stats['expiring_7_days'],
                'expiring_30_days' => (int)$food_stats['expiring_30_days'],
                'expired' => (int)$food_stats['expired']
            ],
            'images' => [
                'total' => (int)$img_stats['total'],
                'total_size' => formatFileSize($img_stats['total_size'])
            ],
            'videos' => [
                'total' => (int)$vid_stats['total'],
                'total_size' => formatFileSize($vid_stats['total_size'])
            ]
        ];
        
        echo json_encode([
            'status' => 'success',
            'data' => $stats
        ], JSON_UNESCAPED_UNICODE);
        
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '獲取儀表板數據失敗: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

// 文件大小格式化函數
function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = floor(log($bytes, 1024));
    
    return round($bytes / pow(1024, $i), 2) . ' ' . $units[$i];
}
?>