<?php

use App\Application;
use App\Controllers\CategoryController;
use App\Controllers\HomeController;
use App\Controllers\PostController;
use App\Database\Database;
use App\Repositories\CategoryRepository;
use App\Repositories\PostRepository;
use App\Routing\Router;
use App\Support\Env;
use App\View\SmartyView;

require __DIR__ . '/../vendor/autoload.php';

Env::load(__DIR__ . '/../.env');

$dbConfig = require __DIR__ . '/../config/database.php';
$appConfig = require __DIR__ . '/../config/app.php';
$pdo = Database::connection($dbConfig);

$categoryRepository = new CategoryRepository($pdo);
$postRepository = new PostRepository($pdo);
$view = new SmartyView(
    __DIR__ . '/../resources/templates',
    __DIR__ . '/../storage/cache/templates_c',
    __DIR__ . '/../storage/cache/cache',
);

$router = new Router();
$router->get('', [HomeController::class, 'index']);
$router->get('category/{slug}', [CategoryController::class, 'show']);
$router->get('post/{slug}', [PostController::class, 'show']);

return new Application(
    $router,
    $categoryRepository,
    $postRepository,
    $view,
    (int) $appConfig['per_page'],
);
