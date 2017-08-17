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

/**
 * Class RegController
 * @package BeeJee\Controllers
 */
class RegController extends PageController
{
    //основная папка проекта
    private $root;
    private $pdo;
    //ошибки user-input'а
    private $errors;
    public $userID = 0;
    
    /**
     * RegController constructor.
     * @param string $root
     * @param \PDO $pdo
     */
    function __construct($root, $pdo)
    {
        parent::__construct();
        $this->root = $root;
        $this->pdo = $pdo;
    }
    
    /**
     * Начало работы  контроллера.
     */
    function start()
    {
        //выполняем все запланированные вне контроллера действия с input массивами
        $this->execute();
        //формируем и отображаем страницу
        $this->regPage($this->root, $this->pdo);
    }
    
    /**
     * Страница регистрации.
     * @param $root
     * @param \PDO $pdo
     */
    protected function regPage($root, \PDO $pdo)
    {
        //маппер таблицы Users
        $mapper    = new UserMapper($pdo);
        //проверяльщик формы регистрации
        $validator = new RegFormValidator($mapper);
        //менеджер лог-инов
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
        //отображаем страницу
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