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
    // 檢查現有表
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    // 定義需要保留的表
    $required_tables = ['food', 'subscription'];
    
    // 定義可以移除的表
    $tables_to_remove = ['users', 'videos', 'images', 'foods', 'subscriptions', 'payments'];
    
    $result = [
        'status' => 'success',
        'message' => '數據庫清理完成',
        'connection' => $connectionTest,
        'existing_tables' => $tables,
        'required_tables' => $required_tables,
        'actions' => []
    ];
    
    // 檢查並移除不必要的表（注意外鍵約束）
    foreach ($tables_to_remove as $table) {
        if (in_array($table, $tables)) {
            try {
                // 先禁用外鍵檢查
                $db->exec("SET FOREIGN_KEY_CHECKS = 0");
                $db->exec("DROP TABLE IF EXISTS `$table`");
                $db->exec("SET FOREIGN_KEY_CHECKS = 1");
                $result['actions'][] = "已移除表: $table";
            } catch(PDOException $e) {
                $result['actions'][] = "移除表 $table 失敗: " . $e->getMessage();
            }
        }
    }
    
    // 檢查必要的表是否存在
    foreach ($required_tables as $table) {
        if (!in_array($table, $tables)) {
            $result['actions'][] = "警告: 必要的表 $table 不存在";
        } else {
            $result['actions'][] = "確認: 必要的表 $table 已存在";
        }
    }
    
    // 重新檢查表結構
    $final_tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $result['final_tables'] = $final_tables;
    
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $exception) {
    echo json_encode([
        'status' => 'error',
        'message' => '清理數據庫錯誤: ' . $exception->getMessage(),
        'connection' => $connectionTest
    ], JSON_UNESCAPED_UNICODE);
}
?>