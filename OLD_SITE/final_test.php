<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 Ultimate Database Connection Test</h1>";

// ============================================================
// 1. LOAD CONFIGURATION
// ============================================================
require_once __DIR__ . '/config/config.php';

echo "<h2>Configuration Values</h2>";
echo "DB_HOST: " . DB_HOST . "<br>";
echo "DB_NAME: " . DB_NAME . "<br>";
echo "DB_USER: " . DB_USER . "<br>";
echo "DB_PASS: " . (DB_PASS ? '***** (set)' : 'EMPTY') . "<br>";
echo "Socket from terminal: /var/lib/mysql/mysql.sock<br>";

// ============================================================
// 2. CHECK PHP'S MYSQL SOCKET CONFIGURATION
// ============================================================
echo "<h2>PHP MySQL Configuration</h2>";
$phpSocket = ini_get('mysqli.default_socket');
echo "mysqli.default_socket: " . ($phpSocket ?: 'NOT SET') . "<br>";

// ============================================================
// 3. TRY CONNECTION METHODS
// ============================================================

$methods = [
    // Method 1: Socket (explicit)
    [
        'name' => 'Socket (explicit)',
        'dsn' => "mysql:unix_socket=/var/lib/mysql/mysql.sock;dbname=" . DB_NAME . ";charset=utf8mb4"
    ],
    // Method 2: Socket from PHP ini
    [
        'name' => 'Socket (PHP ini)',
        'dsn' => "mysql:unix_socket=" . ($phpSocket ?: '/var/lib/mysql/mysql.sock') . ";dbname=" . DB_NAME . ";charset=utf8mb4"
    ],
    // Method 3: localhost (TCP/IP)
    [
        'name' => 'localhost (TCP)',
        'dsn' => "mysql:host=localhost;dbname=" . DB_NAME . ";charset=utf8mb4"
    ],
    // Method 4: 127.0.0.1 (TCP)
    [
        'name' => '127.0.0.1 (TCP)',
        'dsn' => "mysql:host=127.0.0.1;dbname=" . DB_NAME . ";charset=utf8mb4"
    ],
    // Method 5: localhost with port
    [
        'name' => 'localhost:3306 (TCP)',
        'dsn' => "mysql:host=localhost;port=3306;dbname=" . DB_NAME . ";charset=utf8mb4"
    ],
    // Method 6: 127.0.0.1 with port
    [
        'name' => '127.0.0.1:3306 (TCP)',
        'dsn' => "mysql:host=127.0.0.1;port=3306;dbname=" . DB_NAME . ";charset=utf8mb4"
    ]
];

echo "<h2>Testing Connection Methods</h2>";
echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
echo "<tr><th>Method</th><th>Result</th><th>Error</th></tr>";

foreach ($methods as $method) {
    echo "<tr><td><strong>" . $method['name'] . "</strong></td>";
    try {
        $pdo = new PDO($method['dsn'], DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        // Test query
        $stmt = $pdo->query("SELECT NOW() as time, DATABASE() as db");
        $row = $stmt->fetch();
        echo "<td style='color:green;font-weight:bold;'>✅ SUCCESS</td>";
        echo "<td>Server time: " . $row['time'] . "<br>Database: " . $row['db'] . "</td>";
        echo "</tr>";
        // If success, we found the working method
        echo "<tr><td colspan='3' style='background:#d4edda;font-weight:bold;color:green;'>🎉 WORKING METHOD FOUND: " . $method['name'] . "</td></tr>";
        // You can stop here or continue
        // break; // uncomment to stop on first success
    } catch (PDOException $e) {
        echo "<td style='color:red;font-weight:bold;'>❌ FAILED</td>";
        echo "<td style='color:red;'>" . $e->getMessage() . "</td>";
        echo "</tr>";
    }
}
echo "</table>";

// ============================================================
// 4. TRY MYSQLI EXTENSION (ALTERNATIVE)
// ============================================================
echo "<h2>Testing with MySQLi Extension</h2>";
if (function_exists('mysqli_connect')) {
    $hosts = ['localhost', '127.0.0.1'];
    foreach ($hosts as $host) {
        echo "<strong>Testing mysqli with host: $host</strong><br>";
        $mysqli = @new mysqli($host, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_error) {
            echo "❌ mysqli failed: " . $mysqli->connect_error . "<br><br>";
        } else {
            echo "✅ mysqli SUCCESS! Connected to $host<br>";
            $result = $mysqli->query("SELECT NOW() as time");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "Server time: " . $row['time'] . "<br>";
            }
            $mysqli->close();
            echo "<br>";
        }
    }
} else {
    echo "MySQLi extension not available.<br>";
}

// ============================================================
// 5. SQL TO FIX AUTHENTICATION
// ============================================================
echo "<h2>🔧 Recommended Fix</h2>";
echo "If all methods fail, run this SQL in phpMyAdmin to change the authentication plugin:<br>";
echo "<pre style='background:#f4f4f4;padding:10px;border:1px solid #ccc;'>";
echo "ALTER USER '" . DB_USER . "'@'localhost' IDENTIFIED WITH mysql_native_password BY '" . DB_PASS . "';\n";
echo "FLUSH PRIVILEGES;";
echo "</pre>";
echo "Then test again.<br>";

// ============================================================
// 6. CHECK PHP INFO
// ============================================================
echo "<h2>PHP MySQL Extension Info</h2>";
if (function_exists('phpinfo')) {
    ob_start();
    phpinfo(INFO_MODULES);
    $info = ob_get_clean();
    // Extract MySQL section
    if (preg_match('/<h2>mysqlnd.*?<\/h2>.*?<table>.*?<\/table>/s', $info, $matches)) {
        echo $matches[0];
    } elseif (preg_match('/<h2>PDO.*?<\/h2>.*?<table>.*?<\/table>/s', $info, $matches)) {
        echo $matches[0];
    } else {
        echo "Could not extract MySQL info from phpinfo().";
    }
}
?>