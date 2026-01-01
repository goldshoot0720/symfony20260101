<?php
// 載入環境變量
function loadEnv($path) {
    if (!file_exists($path)) {
        return;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// 載入 .env 文件
loadEnv(__DIR__ . '/../.env');

// 數據庫配置
class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $conn;

    public function __construct() {
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->db_name = $_ENV['DB_DATABASE'] ?? 'feng_symfony';
        $this->username = $_ENV['DB_USERNAME'] ?? 'root';
        $this->password = $_ENV['DB_PASSWORD'] ?? '';
    }

    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch(PDOException $exception) {
            echo "連接錯誤: " . $exception->getMessage();
        }
        
        return $this->conn;
    }
    
    public function testConnection() {
        try {
            $conn = $this->getConnection();
            if ($conn) {
                return [
                    'status' => 'success',
                    'message' => '數據庫連接成功',
                    'host' => $this->host,
                    'database' => $this->db_name,
                    'username' => $this->username
                ];
            }
        } catch(Exception $e) {
            return [
                'status' => 'error',
                'message' => '數據庫連接失敗: ' . $e->getMessage(),
                'host' => $this->host,
                'database' => $this->db_name,
                'username' => $this->username
            ];
        }
    }
}

// 數據庫初始化 SQL
function createTables() {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        echo json_encode([
            'status' => 'error',
            'message' => '無法連接到數據庫'
        ], JSON_UNESCAPED_UNICODE);
        return;
    }
    
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
    
    try {
        $db->exec($users_table);
        $db->exec($videos_table);
        $db->exec($images_table);
        $db->exec($foods_table);
        $db->exec($subscriptions_table);
        $db->exec($payments_table);
        
        echo "數據庫表創建成功！\n";
        
        // 插入示例數據
        insertSampleDataForInit($db);
        
    } catch(PDOException $exception) {
        echo "創建表錯誤: " . $exception->getMessage();
    }
}

function insertSampleDataForInit($db) {
    // 插入示例用戶
    $user_sql = "INSERT IGNORE INTO users (username, email, password_hash) VALUES 
                 ('feng_admin', 'feng@example.com', '" . password_hash('admin123', PASSWORD_DEFAULT) . "')";
    $db->exec($user_sql);
    
    // 插入示例食品數據
    $foods_sql = "INSERT IGNORE INTO foods (user_id, name, quantity, price, expiry_date) VALUES 
                  (1, '【張君雅】五香海苔休閒丸子', 3, 0.00, DATE_ADD(CURDATE(), INTERVAL 15 DAY)),
                  (1, '【張君雅】日式串燒休閒丸子', 6, 0.00, DATE_ADD(CURDATE(), INTERVAL 16 DAY))";
    $db->exec($foods_sql);
    
    // 插入示例訂閱數據
    $subscriptions_sql = "INSERT IGNORE INTO subscriptions (user_id, name, service_url, price, next_payment_date, status) VALUES 
                          (1, '天虎/黃信訊/心臟內科', 'https://www.tcmg.com.tw/index.php/main/schedule_time?id=18', 530.00, DATE_ADD(CURDATE(), INTERVAL 25 DAY), 'active'),
                          (1, 'kiro pro', 'https://app.kiro.dev/account/', 640.00, DATE_ADD(CURDATE(), INTERVAL 10 DAY), 'active')";
    $db->exec($subscriptions_sql);
    
    echo "示例數據插入成功！\n";
}

// 如果直接訪問此文件，則創建表
if (basename($_SERVER['PHP_SELF']) == 'database.php') {
    createTables();
}
?>