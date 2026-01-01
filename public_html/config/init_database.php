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

// 如果連接成功，創建表
$db = $database->getConnection();

try {
    // 用戶表
    $users_table = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // 影片表
    $videos_table = "
    CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT 1,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        filename VARCHAR(255) NOT NULL,
        file_path VARCHAR(500) NOT NULL,
        file_size BIGINT,
        duration VARCHAR(20),
        format VARCHAR(10),
        thumbnail_path VARCHAR(500),
        tags JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // 圖片表
    $images_table = "
    CREATE TABLE IF NOT EXISTS images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT 1,
        filename VARCHAR(255) NOT NULL,
        full_name VARCHAR(255),
        file_path VARCHAR(500) NOT NULL,
        file_size BIGINT,
        format VARCHAR(10),
        width INT,
        height INT,
        thumbnail_path VARCHAR(500),
        description TEXT,
        tags JSON,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_format (format),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // 食品表
    $foods_table = "
    CREATE TABLE IF NOT EXISTS foods (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT 1,
        name VARCHAR(255) NOT NULL,
        brand VARCHAR(100),
        category VARCHAR(50),
        quantity INT DEFAULT 1,
        unit VARCHAR(20) DEFAULT '個',
        price DECIMAL(10,2) DEFAULT 0.00,
        purchase_date DATE,
        expiry_date DATE,
        storage_location VARCHAR(100),
        notes TEXT,
        image_path VARCHAR(500),
        status ENUM('新鮮', '即將到期', '已過期') DEFAULT '新鮮',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_expiry_date (expiry_date),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // 訂閱表
    $subscriptions_table = "
    CREATE TABLE IF NOT EXISTS subscriptions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT DEFAULT 1,
        name VARCHAR(255) NOT NULL,
        service_url VARCHAR(500),
        category VARCHAR(50),
        price DECIMAL(10,2) DEFAULT 0.00,
        currency VARCHAR(3) DEFAULT 'TWD',
        billing_cycle ENUM('monthly', 'yearly', 'weekly', 'daily') DEFAULT 'monthly',
        start_date DATE,
        next_payment_date DATE,
        auto_renewal BOOLEAN DEFAULT TRUE,
        status ENUM('active', 'paused', 'cancelled', 'expired', '正常', '即將到期') DEFAULT 'active',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_user_id (user_id),
        INDEX idx_next_payment (next_payment_date),
        INDEX idx_status (status)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // 支付記錄表
    $payments_table = "
    CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        subscription_id INT,
        amount DECIMAL(10,2) NOT NULL,
        currency VARCHAR(3) DEFAULT 'TWD',
        payment_date DATE NOT NULL,
        payment_method VARCHAR(50),
        transaction_id VARCHAR(100),
        status ENUM('completed', 'pending', 'failed', 'refunded') DEFAULT 'completed',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    // 執行創建表的 SQL
    $db->exec($users_table);
    $db->exec($videos_table);
    $db->exec($images_table);
    $db->exec($foods_table);
    $db->exec($subscriptions_table);
    $db->exec($payments_table);
    
    // 插入示例數據
    insertSampleData($db);
    
    echo json_encode([
        'status' => 'success',
        'message' => '數據庫初始化成功',
        'connection' => $connectionTest,
        'tables_created' => ['users', 'videos', 'images', 'foods', 'subscriptions', 'payments'],
        'sample_data' => 'inserted'
    ], JSON_UNESCAPED_UNICODE);
    
} catch(PDOException $exception) {
    echo json_encode([
        'status' => 'error',
        'message' => '創建表錯誤: ' . $exception->getMessage(),
        'connection' => $connectionTest
    ], JSON_UNESCAPED_UNICODE);
}

function insertSampleData($db) {
    try {
        // 插入示例用戶
        $user_sql = "INSERT IGNORE INTO users (id, username, email, password_hash) VALUES 
                     (1, 'feng_admin', 'feng@example.com', ?)";
        $stmt = $db->prepare($user_sql);
        $stmt->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
        
        // 插入示例影片數據
        $videos_sql = "INSERT IGNORE INTO videos (id, title, description, filename, file_path, file_size, duration, format) VALUES 
                      (1, '鋒兄的傳奇人生', '鋒兄人生紀錄片庫存', 'feng_legend.mp4', '/uploads/videos/feng_legend.mp4', 2109440, '00:45', 'MP4'),
                      (2, '鋒兄雜耍Show 🔥', '鋒兄精彩表演庫存', 'feng_show.mp4', '/uploads/videos/feng_show.mp4', 4415488, '01:23', 'MP4')";
        $db->exec($videos_sql);
        
        // 插入示例圖片數據
        $images_sql = "INSERT IGNORE INTO images (id, filename, full_name, file_path, file_size, format) VALUES 
                      (1, '1761405813-e...', '1761405813-eha...', '/uploads/images/1761405813-eha.jpg', 908288, 'JPG'),
                      (2, '1761405863-3...', '1761405863-3ca...', '/uploads/images/1761405863-3ca.jpg', 748544, 'JPG'),
                      (3, '1761405934-7...', '1761405934-740...', '/uploads/images/1761405934-740.jpg', 867328, 'JPG'),
                      (4, '20240917_183...', '20240917_183x2...', '/uploads/images/20240917_183x2.png', 782336000, 'PNG'),
                      (5, '202509_A4_2...', '202509_A4_2.png', '/uploads/images/202509_A4_2.png', 1025507328, 'PNG'),
                      (6, '20251026_214...', '20251026_214x...', '/uploads/images/20251026_214x.jpg', 710656, 'JPG')";
        $db->exec($images_sql);
        
        // 插入示例食品數據
        $foods_sql = "INSERT IGNORE INTO foods (id, name, quantity, price, expiry_date, status) VALUES 
                      (1, '【張君雅】五香海苔休閒丸子', 3, 0.00, DATE_ADD(CURDATE(), INTERVAL 15 DAY), '新鮮'),
                      (2, '【張君雅】日式串燒休閒丸子', 6, 0.00, DATE_ADD(CURDATE(), INTERVAL 16 DAY), '新鮮')";
        $db->exec($foods_sql);
        
        // 插入示例訂閱數據
        $subscriptions_sql = "INSERT IGNORE INTO subscriptions (id, name, service_url, price, next_payment_date, status) VALUES 
                              (1, '天虎/黃信訊/心臟內科', 'https://www.tcmg.com.tw/index.php/main/schedule_time?id=18', 530.00, DATE_ADD(CURDATE(), INTERVAL 25 DAY), '即將到期'),
                              (2, 'kiro pro', 'https://app.kiro.dev/account/', 640.00, DATE_ADD(CURDATE(), INTERVAL 10 DAY), '正常')";
        $db->exec($subscriptions_sql);
        
    } catch(PDOException $e) {
        // 忽略重複插入錯誤
    }
}
?>