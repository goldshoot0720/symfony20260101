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

// 如果連接成功，檢查現有表結構
$db = $database->getConnection();

try {
    // 檢查現有表
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    $result = [
        'status' => 'success',
        'message' => '數據庫檢查完成',
        'connection' => $connectionTest,
        'existing_tables' => $tables,
        'required_tables' => ['food', 'subscription'],
        'table_status' => []
    ];
    
    // 檢查必要的表是否存在
    if (in_array('food', $tables)) {
        $result['table_status']['food'] = '現有表結構已存在，使用現有數據';
        
        // 檢查 food 表結構
        $food_columns = $db->query("DESCRIBE food")->fetchAll(PDO::FETCH_COLUMN);
        $result['table_status']['food_columns'] = $food_columns;
    } else {
        $result['table_status']['food'] = '表不存在，需要創建';
    }
    
    if (in_array('subscription', $tables)) {
        $result['table_status']['subscription'] = '現有表結構已存在，使用現有數據';
        
        // 檢查 subscription 表結構
        $sub_columns = $db->query("DESCRIBE subscription")->fetchAll(PDO::FETCH_COLUMN);
        $result['table_status']['subscription_columns'] = $sub_columns;
    } else {
        $result['table_status']['subscription'] = '表不存在，需要創建';
    }
    
    // 檢查是否有不必要的表
    $unnecessary_tables = array_diff($tables, ['food', 'subscription']);
    if (!empty($unnecessary_tables)) {
        $result['unnecessary_tables'] = $unnecessary_tables;
        $result['message'] .= '，發現不必要的表: ' . implode(', ', $unnecessary_tables);
    }
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $exception) {
    echo json_encode([
        'status' => 'error',
        'message' => '檢查數據庫錯誤: ' . $exception->getMessage(),
        'connection' => $connectionTest
    ], JSON_UNESCAPED_UNICODE);
}

function insertSampleData($db) {
    try {
        // 檢查現有表結構
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        // 如果存在現有的 food 表，不插入示例數據（使用現有數據）
        if (in_array('food', $tables)) {
            $food_count = $db->query("SELECT COUNT(*) FROM food")->fetchColumn();
            if ($food_count == 0) {
                // 只有在表為空時才插入示例數據
                $foods_sql = "INSERT INTO food (name, todate, amount, price, photo) VALUES 
                              ('【張君雅】五香海苔休閒丸子', DATE_ADD(CURDATE(), INTERVAL 15 DAY), 3, 0, 'https://img.pchome.com.tw/cs/items/DBACC4A90089CJA/000001_1689668194.jpg'),
                              ('【張君雅】日式串燒休閒丸子', DATE_ADD(CURDATE(), INTERVAL 16 DAY), 6, 0, 'https://online.carrefour.com.tw/on/demandware.static/-/Sites-carrefour-tw-m-inner/default/dwd792433f/images/large/0246532.jpeg')";
                $db->exec($foods_sql);
            }
        }
        
        // 如果存在現有的 subscription 表，不插入示例數據（使用現有數據）
        if (in_array('subscription', $tables)) {
            $sub_count = $db->query("SELECT COUNT(*) FROM subscription")->fetchColumn();
            if ($sub_count == 0) {
                // 只有在表為空時才插入示例數據
                $subscriptions_sql = "INSERT INTO subscription (name, nextdate, price, site) VALUES 
                                      ('kiro pro', DATE_ADD(CURDATE(), INTERVAL 10 DAY), 640, 'https://app.kiro.dev/account/usage'),
                                      ('自然輸入法/ 已經取消訂閱。', DATE_ADD(CURDATE(), INTERVAL 12 DAY), 129, 'https://service.iqt.ai/AccountInfo')";
                $db->exec($subscriptions_sql);
            }
        }
        
    } catch(PDOException $e) {
        // 忽略重複插入錯誤
    }
}
?>