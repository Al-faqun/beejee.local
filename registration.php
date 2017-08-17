<?php

use BeeJee\Controllers\RegController;
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
    $controller = new RegController($root, $pdo);
    //обработка get параметров
    $controller->get('registered', function ($key, $value, RegController $c) {
        $c->addMessage('Вы успешно зарегистрированы! Теперь можете войти.');
    });
    $controller->start();
    
} catch (\Throwable $e) {
    //если поймана ошибка - разобраться с ней (отобразить или отправить в логи)
    $errorHelper->dispatch($e);
}