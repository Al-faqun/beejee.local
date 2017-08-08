<?php
namespace BeeJee\Controllers;

use BeeJee\Database\TaskMapper;
use BeeJee\Database\UserMapper;
use BeeJee\FileSystem;
use BeeJee\Input\SearchQueryValidator;
use BeeJee\LoginManager;
use BeeJee\Pager;
use BeeJee\SearchData;
use BeeJee\Views\ListView;

class ListController extends PageController
{
    private $root;
    private $pdo;
    
    function __construct($root, $pdo)
    {
        parent::__construct();
        $this->root = $root;
        $this->pdo = $pdo;
    }
    
    function start()
    {
        $this->execute();
        $this->listPage($this->root, $this->pdo);
    }
    
    protected function listPage($root, \PDO $pdo)
    {
        $mapper    = new TaskMapper($pdo);
        $validator = new SearchQueryValidator();
        $pager     = new Pager();
        $messages  = array();
        
        //инициализируем мапперы для бд
        $userMapper = new UserMapper($pdo);
        $loginMan  = new LoginManager($userMapper, $pdo);
        //проверяем логин пользователя (если есть)
        $authorized = $loginMan->isLogged();
        //админ ли?
        $isAdmin = $loginMan->isAdmin();
        //если залогинены - запоминаем имя
        if ($authorized === true) {
            $usernameDisplayed = $loginMan->getLoggedName();
        } else {
            $usernameDisplayed = '';
        }
        
        //получаем данные для db query
        $searchData = $validator->genSearchData($_GET);
        try {
            $pdo->beginTransaction();
            $tasks = $mapper->getTasks($searchData);
        } catch (\Throwable $e) {
            //если ошибка - откатываемся и передаём наверх
            $pdo->rollBack();
            throw new \Exception('Ошибка во время получения задач из бд', 0, $e);
        }
        //превращаем относительные пути к картинкам в абсолютные
        $tasks = $this->setImagePathFull($tasks);
        $entriesCount = $mapper->getEntriesCount();
        //полное число найденных результатов для последнего поискового запроса
        $queries = $pager->getQueries($_GET, $entriesCount);
        $view = new ListView( FileSystem::append([$this->root, 'templates']) );
        $view->render([
            'tasks'    => $tasks,
            'messages' => $this->messages,
            'queries'  => $queries,
            'authorized' => $authorized,
            'is_admin' => $isAdmin,
            'username'   => $usernameDisplayed
        ]);
    }
    
    private function setImagePathFull($tasks)
    {
        if (is_array($tasks) AND !empty($tasks)) {
            foreach ($tasks as $task) {
                $fullpath = "http://" . $_SERVER['SERVER_NAME'] . '/uploads/' . $task->getImgPathRel();
                $task->setImgPathRel($fullpath);
            }
        }
        return $tasks;
    }
}