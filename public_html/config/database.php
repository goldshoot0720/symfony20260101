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

// 檢查現有表結構並創建兼容性視圖
function checkAndCreateCompatibilityViews() {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        return [
            'status' => 'error',
            'message' => '無法連接到數據庫'
        ];
    }
    
    try {
        // 檢查現有表
        $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        $result = [
            'status' => 'success',
            'message' => '數據庫檢查完成',
            'existing_tables' => $tables,
            'compatibility' => []
        ];
        
        // 如果存在 food 表，創建兼容性映射
        if (in_array('food', $tables)) {
            $result['compatibility']['foods'] = 'food 表已存在，將使用現有結構';
        }
        
        // 如果存在 subscription 表，創建兼容性映射
        if (in_array('subscription', $tables)) {
            $result['compatibility']['subscriptions'] = 'subscription 表已存在，將使用現有結構';
        }
        
        return $result;
        
    } catch(PDOException $e) {
        return [
            'status' => 'error',
            'message' => '檢查表結構錯誤: ' . $e->getMessage()
        ];
    }
}

// 如果直接訪問此文件，則檢查數據庫
if (basename($_SERVER['PHP_SELF']) == 'database.php') {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(checkAndCreateCompatibilityViews(), JSON_UNESCAPED_UNICODE);
}
?>