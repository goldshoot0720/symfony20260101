<?php
require_once 'database.php';

header('Content-Type: application/json; charset=utf-8');

// 測試數據庫連接
$database = new Database();
$connectionTest = $database->testConnection();

if ($connectionTest['status'] === 'error') {
    echo json_encode($connectionTest, JSON_UNESCAPED_UNICODE);
    exit;
}

$db = $database->getConnection();

try {
    // 創建 food 表（使用現有結構）
    $food_table = "
    CREATE TABLE IF NOT EXISTS `food` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` text,
        `todate` date DEFAULT NULL,
        `amount` int DEFAULT NULL,
        `photo` text,
        `price` int DEFAULT NULL,
        `shop` text,
        `photohash` text,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    
    // 創建 subscription 表（使用現有結構）
    $subscription_table = "
    CREATE TABLE IF NOT EXISTS `subscription` (
        `id` int NOT NULL AUTO_INCREMENT,
        `name` text,
        `nextdate` date DEFAULT NULL,
        `price` int DEFAULT NULL,
        `site` text,
        `note` text,
        `account` text,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";
    
    // 執行創建表的 SQL
    $db->exec($food_table);
    $db->exec($subscription_table);
    
    // 插入現有數據
    insertExistingData($db);
    
    $result = [
        'status' => 'success',
        'message' => '必要表創建成功',
        'connection' => $connectionTest,
        'tables_created' => ['food', 'subscription'],
        'data_inserted' => true
    ];
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $exception) {
    echo json_encode([
        'status' => 'error',
        'message' => '創建表錯誤: ' . $exception->getMessage(),
        'connection' => $connectionTest
    ], JSON_UNESCAPED_UNICODE);
}

function insertExistingData($db) {
    try {
        // 插入現有的 food 數據
        $food_sql = "INSERT INTO `food` (`id`, `name`, `todate`, `amount`, `photo`, `price`, `shop`, `photohash`) VALUES
            (1, '【張君雅】五香海苔休閒丸子', '2026-01-06', 3, 'https://img.pchome.com.tw/cs/items/DBACC4A90089CJA/000001_1689668194.jpg', NULL, NULL, NULL),
            (2, '【張君雅】日式串燒休閒丸子', '2026-01-07', 6, 'https://online.carrefour.com.tw/on/demandware.static/-/Sites-carrefour-tw-m-inner/default/dwd792433f/images/large/0246532.jpeg', 0, '', '')
            ON DUPLICATE KEY UPDATE name=VALUES(name)";
        $db->exec($food_sql);
        
        // 插入現有的 subscription 數據
        $subscription_sql = "INSERT INTO `subscription` (`id`, `name`, `nextdate`, `price`, `site`, `note`, `account`) VALUES
            (1, 'kiro pro', '2026-01-01', 640, 'https://app.kiro.dev/account/usage', NULL, NULL),
            (2, '自然輸入法/ 已經取消訂閱。', '2026-01-03', 129, 'https://service.iqt.ai/AccountInfo', '', '')
            ON DUPLICATE KEY UPDATE name=VALUES(name)";
        $db->exec($subscription_sql);
        
    } catch(PDOException $e) {
        // 忽略重複插入錯誤
    }
}
?>