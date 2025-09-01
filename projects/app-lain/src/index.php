<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>App Lain - Project Structure (PHP 8.2)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            color: white;
        }
        .container {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .info {
            background: rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 5px;
            margin: 10px 0;
        }
        .success {
            color: #4ade80;
            font-weight: bold;
        }
        .warning {
            color: #fbbf24;
            font-weight: bold;
        }
        .code {
            background: rgba(0, 0, 0, 0.3);
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            margin: 10px 0;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 App Lain - Project Structure (PHP 8.2)</h1>
        
        <div class="info">
            <h2>✅ PHP Status</h2>
            <p><strong>PHP Version:</strong> <?php echo phpversion(); ?></p>
            <p><strong>Server Time:</strong> <?php echo date('Y-m-d H:i:s'); ?></p>
            <p><strong>Timezone:</strong> <?php echo date_default_timezone_get(); ?></p>
        </div>

        <div class="info">
            <h2>🔗 Database Connection Test</h2>
            <?php
            try {
                $pdo = new PDO(
                    "mysql:host=app-lain-mysql;dbname=app_lain;charset=utf8mb4",
                    "app_lain",
                    "supersecretuser",
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    ]
                );
                
                echo "<div class='success'>✅ Database connection successful!</div>";
                
                // Test query
                $stmt = $pdo->query("SELECT VERSION() as version");
                $result = $stmt->fetch();
                echo "<p><strong>MySQL Version:</strong> " . $result['version'] . "</p>";
                
                // Create test table
                $pdo->exec("CREATE TABLE IF NOT EXISTS project_test (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    message VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )");
                
                // Insert test data
                $stmt = $pdo->prepare("INSERT INTO project_test (message) VALUES (?)");
                $stmt->execute(['Hello from App Lain Project Structure! ' . date('Y-m-d H:i:s')]);
                
                echo "<div class='success'>✅ Test data inserted successfully!</div>";
                
            } catch (PDOException $e) {
                echo "<div class='warning'>❌ Database Error: " . $e->getMessage() . "</div>";
            }
            ?>
        </div>

        <div class="info">
            <h2>🔴 Redis Connection Test</h2>
            <?php
            try {
                $redis = new Redis();
                $redis->connect('shared-redis', 6379);
                
                echo "<div class='success'>✅ Redis connection successful!</div>";
                
                // Test Redis operations
                $redis->set('app_lain_project', 'Hello from App Lain via Shared Redis! ' . date('Y-m-d H:i:s'));
                $value = $redis->get('app_lain_project');
                
                echo "<p><strong>Redis Test:</strong> " . htmlspecialchars($value) . "</p>";
                
            } catch (Exception $e) {
                echo "<div class='warning'>❌ Redis Error: " . $e->getMessage() . "</div>";
            }
            ?>
        </div>

        <div class="info">
            <h2>🔧 PHP Configuration</h2>
            <p><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></p>
            <p><strong>Upload Max Filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
            <p><strong>Post Max Size:</strong> <?php echo ini_get('post_max_size'); ?></p>
            <p><strong>Max Execution Time:</strong> <?php echo ini_get('max_execution_time'); ?>s</p>
        </div>

        <div class="info">
            <h2>🏗️ Project Structure</h2>
            <div class="code">
projects/app-lain/
├── .env (from env.example)
├── docker-compose.yml
├── docker/
│   └── Dockerfile
├── config/
│   ├── php.ini
│   └── mysql.cnf
├── src/
│   ├── index.php
│   └── .htaccess
└── data/
    └── mysql/
            </div>
        </div>

        <div class="info">
            <h2>🌐 Server Information</h2>
            <p><strong>App Name:</strong> App Lain</p>
            <p><strong>PHP Version:</strong> 8.2</p>
            <p><strong>Database Host:</strong> app-lain-mysql (isolated)</p>
            <p><strong>Redis Host:</strong> shared-redis (shared)</p>
            <p><strong>Request URI:</strong> <?php echo $_SERVER['REQUEST_URI'] ?? '/'; ?></p>
        </div>
    </div>
</body>
</html>
