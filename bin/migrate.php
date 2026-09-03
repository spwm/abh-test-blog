<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Database\Database;
use App\Database\MigrationRunner;
use App\Support\Env;

Env::load(__DIR__ . '/../.env');

$config = require __DIR__ . '/../config/database.php';
$pdo = Database::connection($config);

$runner = new MigrationRunner($pdo, __DIR__ . '/../database/migrations');
$applied = $runner->run();

if ($applied === []) {
    echo "Nothing to migrate.\n";
} else {
    foreach ($applied as $migration) {
        echo "Applied: {$migration}\n";
    }
}
