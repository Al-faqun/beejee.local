<?php
namespace BeeJee\Controllers;


use BeeJee\Database\UserMapper;
use BeeJee\LoginManager;

class LoginController extends PageController
{
    //главная папка проекта
    private $root;
    private $pdo;
    
    /**
     * LoginController constructor.
     * @param string $root
     * @param \PDO $pdo
     */
    function __construct($root, $pdo)
    {
        parent::__construct();
        $this->root = $root;
        $this->pdo = $pdo;
    }
    
    function start()
    {
        //маппер таблицы users
        $mapper    = new UserMapper($this->pdo);
        //менеджер лог-инов
        $loginMan  = new LoginManager($mapper, $this->pdo);
        //проверяем, правильные ли отослал пользователь данные
        $userID = $loginMan->checkLoginForm($_POST);
        if ($userID !== false ) {
            //если правильные - сохраняем его логин в куки
            $loginMan->persistLogin($userID);
        }
        //в конце всех действий - редирект на главную страницу
        $this->redirect('list.php');
    }
    
    /**
     * Разлогиниваем пользователя
     */
    function logout()
    {
        $mapper    = new UserMapper($this->pdo);;
        $loginMan  = new LoginManager($mapper, $this->pdo);
        $loginMan->logout();
    }
}