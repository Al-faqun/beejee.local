<?php
namespace BeeJee\Controllers;


use BeeJee\Database\TaskMapper;
use BeeJee\Database\UserMapper;
use BeeJee\FileSystem;
use BeeJee\Input\NewTaskValidator;
use BeeJee\LoginManager;
use BeeJee\Views\EditView;

/**
 * Class EditController
 * @package BeeJee\Controllers
 */
class EditController extends PageController
{
    //корень сайта
    private $root;
    private $pdo;
    
    /**
     * EditController constructor.
     * @param $root
     * @param $pdo
     */
    function __construct($root, $pdo)
    {
        parent::__construct();
        $this->root = $root;
        $this->pdo = $pdo;
    }
    
    /**
     * Основные задачи контроллера
     */
    function start()
    {
        //для редактирования задачи
        $taskmapper = new TaskMapper($this->pdo);
        //проверяем, имеет ли пользователь права на редактирование
        $userMapper = new UserMapper($this->pdo);
        $loginMan  = new LoginManager($userMapper, $this->pdo);
        //проверяем логин пользователя (если есть)
        $authorized = $loginMan->isLogged();
        //админ ли?
        $isAdmin = $loginMan->isAdmin();
        if ($authorized AND $isAdmin) {
            $statusChanged = $this->checkAndChangeStatus($_POST, $taskmapper);
            //проверяем в инпуте наличие айди, без него - исключение
            $taskID = $this->checkTaskID($_POST);
            $editResult = $this->checkAndChangeNewText($_POST, $taskmapper);
            //если успешно отредактировали текст -> возвращаемся на главную
            if ($editResult) {
                $this->redirect('list.php');
            } else {
                //если нет - показываем окошко редактирования
                $taskText = $taskmapper->getTask($taskID)->getText();
                $view = new EditView(FileSystem::append([$this->root, 'templates']));
                $view->render([
                    'authorized' => $authorized,
                    'task_id' => $taskID,
                    'task_text' => $taskText
                ]);
            }
        }
    }
    
    /**
     * Меняем статус задачи, если соответствующий ключ есть в инпуте
     * @param array $input
     * @param TaskMapper $taskmapper
     * @return bool
     */
    function checkAndChangeStatus($input, TaskMapper $taskmapper)
    {
        $result = false;
        if (array_key_exists('fulfilled', $input) AND $input['fulfilled'] === '1') {
            if (array_key_exists('task_id', $input)) {
                $result = $taskmapper->changeStatus((int)$input['task_id'], true);
            }
        }
        return $result;
    }
    
    /**
     * Проверяем, указан ли айди задачи перед редактированием
     * @param array $input
     * @return int ID of task
     * @throws \Exception
     */
    function checkTaskID($input)
    {
        if (array_key_exists('task_id', $input)) {
            return (int)$input['task_id'];
        } else throw new \Exception('ID задачи не указан перед редактированием. Аборт.');
    }
    
    /**
     * Проверяем, отослан ли текст для редактирования, при необходимости его заменяем
     * @param array $input
     * @param TaskMapper $taskmapper
     * @return bool
     */
    function checkAndChangeNewText($input, TaskMapper $taskmapper )
    {
        //если отослана форма редактирования вместе с ID задачи
        if (array_key_exists('edit_form_sent', $input)
              AND
            $input['edit_form_sent'] === '1'
              AND
            $taskID = $this->checkTaskID($input)
        ) {
            $validator = new NewTaskValidator();
            $newText = $validator->checkTaskText($input);
            $result = $taskmapper->changeText($taskID, $newText);
        } else $result = false;
        return $result;
    }
}