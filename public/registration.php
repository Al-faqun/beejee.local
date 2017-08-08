<?php

use BeeJee\Controllers\RegController;
use BeeJee\ErrorHelper;
use BeeJee\FileSystem;

$root = dirname(__FILE__, 2);
//автозагрузчик и объект PDO
require_once ($root . '/bootstrap.php');
//обработчик ошибок
$errorHelper = new ErrorHelper(FileSystem::append([$root, 'templates']));
try {
    $controller = new RegController($root, $pdo);
    //обработка get параметров
    $controller->get('registered', function ($key, $value, RegController $c) {
        $c->addMessage('Вы успешно зарегистрированы! Теперь можете войти.');
    });
    $controller->start();
    
} catch (\Throwable $e) {
    $errorHelper->dispatch($e);
}