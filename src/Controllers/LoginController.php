<?php
namespace BeeJee\Controllers;


use BeeJee\Database\UserMapper;
use BeeJee\LoginManager;

class LoginController extends PageController
{
    private $root;
    private $pdo;
    private $is_logged;
    function __construct($root, $pdo)
    {
        parent::__construct();
        $this->root = $root;
        $this->pdo = $pdo;
    }
    
    function start()
    {
        $mapper    = new UserMapper($this->pdo);;
        $loginMan  = new LoginManager($mapper, $this->pdo);
        $userID = $loginMan->checkLoginForm($_POST);
        if ($userID !== false ) {
            $loginMan->persistLogin($userID);
        }
        //в конце всех действий - редирект на главную страницу
        $this->redirect('list.php');
    }
    
    function logout()
    {
        $mapper    = new UserMapper($this->pdo);;
        $loginMan  = new LoginManager($mapper, $this->pdo);
        $loginMan->logout();
    }
}