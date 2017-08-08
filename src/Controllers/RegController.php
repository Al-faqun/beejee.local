<?php
/**
 * Created by PhpStorm.
 * User: Shinoa
 * Date: 07.08.2017
 * Time: 0:49
 */

namespace BeeJee\Controllers;


use BeeJee\Database\UserMapper;
use BeeJee\FileSystem;
use BeeJee\Input\RegFormValidator;
use BeeJee\LoginManager;
use BeeJee\Views\RegView;

class RegController extends PageController
{
    private $root;
    private $pdo;
    private $errors;
    public $userID = 0;
    
    function __construct($root, $pdo)
    {
        parent::__construct();
        $this->root = $root;
        $this->pdo = $pdo;
    }
    
    function start()
    {
        $this->execute();
        $this->regPage($this->root, $this->pdo);
    }
    
    protected function regPage($root, \PDO $pdo)
    {
        $mapper    = new UserMapper($pdo);
        $validator = new RegFormValidator($mapper);
        $loginMan  = new LoginManager($mapper, $pdo);
        //проверяем логин пользователя (если есть)
        $authorized = $loginMan->isLogged();
        //если залогинены - запоминаем имя
        if ($authorized === true) {
            $usernameDisplayed = $loginMan->getLoggedName();
        } else {
            $usernameDisplayed = '';
        }
        $dataBack  = array();  // значения неправильных входных данных
        //проверяем, были ли посланы данные формы
        if ($validator->dataSent($_POST)) {
            //проверяем, правильно ли они заполнены
            $data = $validator->checkInput($_POST, $this->errors);
            if ($data !== false) {
                $user = $loginMan->registerUser($data['username'], $data['password']);
                $this->redirect('registration.php?registered');
            } else {
                $dataBack['username'] = $_POST['username'];
            }
        }
        $view = new RegView(FileSystem::append([$root, '/templates']));
        $view->render([
            'errors'     => $this->errors,
            'messages'   => $this->messages,
            'databack'   => $dataBack,
            'authorized' => $authorized,
            'username'   => $usernameDisplayed
        ]);
    }
}