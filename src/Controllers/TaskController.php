<?php
/**
 * Created by PhpStorm.
 * User: Shinoa
 * Date: 07.08.2017
 * Time: 19:11
 */

namespace BeeJee\Controllers;


use BeeJee\Database\TaskMapper;
use BeeJee\Database\UserMapper;
use BeeJee\FileSystem;
use BeeJee\Input\ImageLoaderBase64;
use BeeJee\Input\NewTaskValidator;
use BeeJee\LoginManager;
use BeeJee\Views\TaskView;

class TaskController extends PageController
{
    private $root;
    private $public;
    private $pdo;
    private $errors;
    
    function __construct($root, $public, $pdo)
    {
        parent::__construct();
        $this->root = $root;
        $this->public = $public;
        $this->pdo = $pdo;
    }
    
    function start()
    {
        $this->execute();
        $this->regPage($this->root, $this->public, $this->pdo);
    }
    
    protected function regPage($root, $public, \PDO $pdo)
    {
        $userMapper = new UserMapper($pdo);
        $taskMapper = new TaskMapper($pdo);
        $validator = new NewTaskValidator();
        $loginMan  = new LoginManager($userMapper, $pdo);
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
                //если пользователь авторизован - используем его аккаунт, иначе аккаунт Гостя
                $taskUsername = $authorized ? $usernameDisplayed : 'Guest';
                $userID = $userMapper->getIdFromName($taskUsername);
                //сохраняем картинку
                $data['img_path_rel'] = $this->saveImage($public, 'uploads', $data['imageBase64']);
                //добавляем запись с расчитанными и проверенными параметрами
                $taskMapper->addTask($userMapper, $userID, $data['email'], $data['task_text'], $data['img_path_rel']);
                $this->redirect('list.php?taskAdded');
            } else {
                $dataBack['email'] = $_POST['email'];
                $dataBack['task_text'] = $_POST['task_text'];
            }
        }
        
        $view = new TaskView(FileSystem::append([$root, '/templates']));
        $view->render([
            'errors'     => $this->errors,
            'messages'   => $this->messages,
            'databack'   => $dataBack,
            'authorized' => $authorized,
            'username'   => $usernameDisplayed
        ]);
    }
    
    protected function saveImage($root, $dir, $imageBase64)
    {
        $imageLoader = new ImageLoaderBase64(
            array('image/jpeg', 'image/png', 'image/gif'),
            array('jpg', 'jpeg', 'png', 'gif')
        );
        $saveDir = FileSystem::append([$root, $dir]);
        $fileName = $imageLoader->saveFile($imageBase64, 'png', $saveDir);
        if ($fileName !== false) {
            return $fileName;
        } else throw new \Exception("Cannot save image at $saveDir");
    }
}