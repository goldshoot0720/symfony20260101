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
        '/foods' => 'getFoods',
        '/subscriptions' => 'getSubscriptions',
        '/dashboard' => 'getDashboardStats'
    ],
    'POST' => [
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
        'timestamp' => date('Y-m-d H:i:s'),
        'supported_modules' => ['foods', 'subscriptions']
    ]);
}

function getFoods() {
    global $db;
    
    try {
        if ($db) {
            // 檢查是否存在 food 表（現有結構）
            $tables = $db->query("SHOW TABLES LIKE 'food'")->fetchAll();
            
            if (count($tables) > 0) {
                // 使用現有的 food 表結構
                $stmt = $db->query("
                    SELECT 
                        id,
                        name,
                        todate as expiry_date,
                        amount as quantity,
                        price,
                        photo,
                        shop,
                        DATEDIFF(todate, CURDATE()) as days_remaining,
                        CASE 
                            WHEN DATEDIFF(todate, CURDATE()) < 0 THEN '已過期'
                            WHEN DATEDIFF(todate, CURDATE()) <= 3 THEN '即將到期'
                            ELSE '新鮮'
                        END as status
                    FROM food 
                    ORDER BY todate ASC
                ");
                $foods = $stmt->fetchAll();
            } else {
                // 使用新的 foods 表結構
                $stmt = $db->query("
                    SELECT *, 
                    DATEDIFF(expiry_date, CURDATE()) as days_remaining 
                    FROM foods 
                    ORDER BY expiry_date ASC
                ");
                $foods = $stmt->fetchAll();
            }
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
                    'status' => '新鮮',
                    'photo' => 'https://img.pchome.com.tw/cs/items/DBACC4A90089CJA/000001_1689668194.jpg'
                ],
                [
                    'id' => 2,
                    'name' => '【張君雅】日式串燒休閒丸子',
                    'quantity' => 6,
                    'price' => 0,
                    'expiry_date' => '2026-01-07',
                    'days_remaining' => 16,
                    'status' => '新鮮',
                    'photo' => 'https://online.carrefour.com.tw/on/demandware.static/-/Sites-carrefour-tw-m-inner/default/dwd792433f/images/large/0246532.jpeg'
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
            // 檢查是否存在 subscription 表（現有結構）
            $tables = $db->query("SHOW TABLES LIKE 'subscription'")->fetchAll();
            
            if (count($tables) > 0) {
                // 使用現有的 subscription 表結構
                $stmt = $db->query("
                    SELECT 
                        id,
                        name,
                        nextdate as next_payment_date,
                        price,
                        site as service_url,
                        note,
                        account,
                        DATEDIFF(nextdate, CURDATE()) as days_remaining,
                        CASE 
                            WHEN DATEDIFF(nextdate, CURDATE()) < 0 THEN '已過期'
                            WHEN DATEDIFF(nextdate, CURDATE()) <= 7 THEN '即將到期'
                            ELSE '正常'
                        END as status
                    FROM subscription 
                    ORDER BY nextdate ASC
                ");
                $subscriptions = $stmt->fetchAll();
            } else {
                // 使用新的 subscriptions 表結構
                $stmt = $db->query("
                    SELECT *, 
                    DATEDIFF(next_payment_date, CURDATE()) as days_remaining 
                    FROM subscriptions 
                    ORDER BY next_payment_date ASC
                ");
                $subscriptions = $stmt->fetchAll();
            }
        } else {
            // 模擬數據
            $subscriptions = [
                [
                    'id' => 1,
                    'name' => 'kiro pro',
                    'service_url' => 'https://app.kiro.dev/account/usage',
                    'price' => 640,
                    'next_payment_date' => '2026-01-01',
                    'days_remaining' => 10,
                    'status' => '正常'
                ],
                [
                    'id' => 2,
                    'name' => '自然輸入法/ 已經取消訂閱。',
                    'service_url' => 'https://service.iqt.ai/AccountInfo',
                    'price' => 129,
                    'next_payment_date' => '2026-01-03',
                    'days_remaining' => 12,
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

function createFood() {
    global $db;
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        if ($db) {
            // 檢查是否存在 food 表
            $tables = $db->query("SHOW TABLES LIKE 'food'")->fetchAll();
            
            if (count($tables) > 0) {
                // 使用現有的 food 表結構
                $stmt = $db->prepare("INSERT INTO food (name, todate, amount, price, photo, shop) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['name'] ?? '',
                    $input['expiry_date'] ?? null,
                    $input['quantity'] ?? 1,
                    $input['price'] ?? 0,
                    $input['photo'] ?? '',
                    $input['shop'] ?? ''
                ]);
                
                echo json_encode([
                    'status' => 'success',
                    'message' => '食品新增成功',
                    'id' => $db->lastInsertId()
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'food 表不存在'
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => '數據庫連接失敗'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '新增食品失敗: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function createSubscription() {
    global $db;
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        if ($db) {
            // 檢查是否存在 subscription 表
            $tables = $db->query("SHOW TABLES LIKE 'subscription'")->fetchAll();
            
            if (count($tables) > 0) {
                // 使用現有的 subscription 表結構
                $stmt = $db->prepare("INSERT INTO subscription (name, nextdate, price, site, note, account) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $input['name'] ?? '',
                    $input['next_payment_date'] ?? null,
                    $input['price'] ?? 0,
                    $input['service_url'] ?? '',
                    $input['note'] ?? '',
                    $input['account'] ?? ''
                ]);
                
                echo json_encode([
                    'status' => 'success',
                    'message' => '訂閱新增成功',
                    'id' => $db->lastInsertId()
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'subscription 表不存在'
                ], JSON_UNESCAPED_UNICODE);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => '數據庫連接失敗'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch(Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => '新增訂閱失敗: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}

function getDashboardStats() {
    global $db;
    
    try {
        if ($db) {
            // 檢查現有表結構
            $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            // 獲取訂閱統計
            if (in_array('subscription', $tables)) {
                // 使用現有的 subscription 表
                $sub_stmt = $db->query("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN DATEDIFF(nextdate, CURDATE()) <= 3 THEN 1 ELSE 0 END) as expiring_3_days,
                        SUM(CASE WHEN DATEDIFF(nextdate, CURDATE()) <= 7 THEN 1 ELSE 0 END) as expiring_7_days,
                        SUM(CASE WHEN nextdate < CURDATE() THEN 1 ELSE 0 END) as expired
                    FROM subscription
                ");
                $sub_stats = $sub_stmt->fetch();
            } else {
                $sub_stats = ['total' => 0, 'expiring_3_days' => 0, 'expiring_7_days' => 0, 'expired' => 0];
            }
            
            // 獲取食品統計
            if (in_array('food', $tables)) {
                // 使用現有的 food 表
                $food_stmt = $db->query("
                    SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN DATEDIFF(todate, CURDATE()) <= 3 THEN 1 ELSE 0 END) as expiring_3_days,
                        SUM(CASE WHEN DATEDIFF(todate, CURDATE()) <= 7 THEN 1 ELSE 0 END) as expiring_7_days,
                        SUM(CASE WHEN DATEDIFF(todate, CURDATE()) <= 30 THEN 1 ELSE 0 END) as expiring_30_days,
                        SUM(CASE WHEN todate < CURDATE() THEN 1 ELSE 0 END) as expired
                    FROM food
                ");
                $food_stats = $food_stmt->fetch();
            } else {
                $food_stats = ['total' => 0, 'expiring_3_days' => 0, 'expiring_7_days' => 0, 'expiring_30_days' => 0, 'expired' => 0];
            }
            
        } else {
            // 模擬數據
            $sub_stats = ['total' => 2, 'expiring_3_days' => 0, 'expiring_7_days' => 0, 'expired' => 0];
            $food_stats = ['total' => 2, 'expiring_3_days' => 0, 'expiring_7_days' => 0, 'expiring_30_days' => 0, 'expired' => 0];
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