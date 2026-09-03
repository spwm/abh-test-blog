<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Database\Database;
use App\Support\Env;
use Database\Seeders\DatabaseSeeder;

Env::load(__DIR__ . '/../.env');

$config = require __DIR__ . '/../config/database.php';
$pdo = Database::connection($config);

(new DatabaseSeeder($pdo))->run();

echo "Seeding complete.\n";
