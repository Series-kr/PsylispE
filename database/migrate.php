<?php
require_once __DIR__ . '/../app/Config/Env.php';
require_once __DIR__ . '/../app/Config/Database.php';

App\Config\Env::load(__DIR__ . '/../.env');
$pdo = App\Config\Database::getInstance();

// Get all migration files
$migrations = glob(__DIR__ . '/migrations/*.php');
sort($migrations);

// Create migrations table if not exists
$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Get already run migrations
$stmt = $pdo->query("SELECT migration FROM migrations");
$runMigrations = $stmt->fetchAll(\PDO::FETCH_COLUMN);

$batch = 1;
foreach ($migrations as $migration) {
    $migrationName = basename($migration);
    
    if (!in_array($migrationName, $runMigrations)) {
        echo "Running migration: $migrationName\n";
        
        require_once $migration;
        $className = str_replace('.php', '', $migrationName);
        $className = str_replace('_', ' ', $className);
        $className = ucwords($className);
        $className = str_replace(' ', '', $className);
        
        $migrationInstance = new $className();
        $migrationInstance->up($pdo);
        
        // Record migration
        $stmt = $pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (?, ?)");
        $stmt->execute([$migrationName, $batch]);
        
        $batch++;
    }
}

echo "All migrations completed successfully!\n";