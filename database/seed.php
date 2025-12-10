<?php
require_once __DIR__ . '/../app/Config/Env.php';
require_once __DIR__ . '/../app/Config/Database.php';

App\Config\Env::load(__DIR__ . '/../.env');
$pdo = App\Config\Database::getInstance();

require_once __DIR__ . '/seeds/DatabaseSeeder.php';

$seeder = new DatabaseSeeder($pdo);
$seeder->run();

echo "Database seeding completed successfully!\n";