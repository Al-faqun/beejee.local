<?php

use BeeJee\Controllers\LoginController;
use BeeJee\ErrorHelper;
use BeeJee\FileSystem;

$root = dirname(__FILE__, 2);
//автозагрузчик и объект PDO
require_once ($root . '/bootstrap.php');
//обработчик ошибок
$errorHelper = new ErrorHelper(FileSystem::append([$root, 'templates']));
try {
    $controller = new LoginController($root, $pdo);
    $controller->start();
    
} catch (\Throwable $e) {
    $errorHelper->dispatch($e);
}