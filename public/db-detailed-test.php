<?php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<pre style='font-family: monospace; font-size: 14px;'>";
echo "=== DETAILED MYSQL CONNECTION TEST ===\n\n";

// 1. Покажем конфигурацию
echo "1. DATABASE CONFIGURATION:\n";
$config = config('database.connections.mysql');
foreach ($config as $key => $value) {
    if ($key === 'password') {
        echo "   $key: " . (($value) ? '***SET***' : 'NULL') . "\n";
    } else {
        echo "   $key: " . (is_array($value) ? json_encode($value) : $value) . "\n";
    }
}
echo "\n";

// 2. Проверим подключение
echo "2. CONNECTION TEST:\n";
try {
    $pdo = DB::connection()->getPdo();
    echo "   ✓ SUCCESS: Connected to database\n";
    echo "   Database: " . DB::connection()->getDatabaseName() . "\n";
    echo "   Driver: " . DB::connection()->getDriverName() . "\n";
    echo "   Server version: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n\n";
} catch (Exception $e) {
    echo "   ✗ ERROR: " . $e->getMessage() . "\n\n";
    exit;
}

// 3. Проверим таблицу clients
echo "3. CLIENTS TABLE CHECK:\n";
try {
    $tables = DB::select("SHOW TABLES LIKE 'clients'");
    if (count($tables) > 0) {
        echo "   ✓ Clients table exists\n";
        
        // Проверим структуру
        $columns = DB::select("DESCRIBE clients");
        echo "   Table structure:\n";
        foreach ($columns as $col) {
            echo "     - {$col->Field} ({$col->Type})\n";
        }
        echo "\n";
        
        // Проверим данные
        $count = DB::table('clients')->count();
        echo "   Total records: $count\n";
        
        if ($count > 0) {
            $sample = DB::table('clients')->first();
            echo "   Sample record:\n";
            foreach ((array)$sample as $key => $value) {
                echo "     $key: $value\n";
            }
        }
    } else {
        echo "   ! Clients table does not exist\n";
    }
} catch (Exception $e) {
    echo "   ! Error checking clients table: " . $e->getMessage() . "\n";
}
echo "\n";

// 4. Проверим права пользователя
echo "4. DATABASE USER PERMISSIONS:\n";
try {
    $grants = DB::select("SHOW GRANTS FOR CURRENT_USER()");
    echo "   Current user grants:\n";
    foreach ($grants as $grant) {
        foreach ((array)$grant as $line) {
            echo "     - " . $line . "\n";
        }
    }
} catch (Exception $e) {
    echo "   ! Cannot check grants: " . $e->getMessage() . "\n";
}
echo "\n";

// 5. Системная информация
echo "5. SYSTEM INFORMATION:\n";
echo "   Web server user: " . shell_exec('whoami') . "\n";
echo "   PHP process user: " . posix_getpwuid(posix_geteuid())['name'] . "\n";
echo "   PHP version: " . PHP_VERSION . "\n";
echo "   Laravel version: " . app()->version() . "\n";
echo "   Current time: " . date('Y-m-d H:i:s') . "\n";

echo "\n=== TEST COMPLETE ===\n";
echo "</pre>";
?>
