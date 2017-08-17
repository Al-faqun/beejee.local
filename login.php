<?php

use BeeJee\Controllers\LoginController;
use BeeJee\ErrorHelper;
use BeeJee\FileSystem;

//главная папка проекта
$root = dirname(__FILE__, 1);
//автозагрузчик и объект PDO
require_once ($root . '/bootstrap.php');
//обработчик ошибок
$errorHelper = new ErrorHelper(FileSystem::append([$root, 'templates']));
try {
    //вызываем нужный контроллер
    $controller = new LoginController($root, $pdo);
    $controller->start();
    
} catch (\Throwable $e) {
    //если поймана ошибка - разобраться с ней (отобразить или отправить в логи)
    $errorHelper->dispatch($e);
}