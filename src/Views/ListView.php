<?php
namespace BeeJee\Views;


use BeeJee\FileSystem;

class ListView extends CommonView
{
    /**
     * StudentListView constructor.
     * @param string $templatesDir
     */
    function __construct($templatesDir)
    {
        parent::__construct($templatesDir);
        $loader = new \Twig_Loader_Filesystem([FileSystem::append([$templatesDir, 'List']), $templatesDir]);
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => FileSystem::append([$templatesDir, 'cache']),
            'auto_reload' => true,
            'autoescape' => 'html',
            'strict_variables' => true
        ));
    }
    
    /**
     * Loads all values and preferences for a template, then loads the template into string.
     * @var $params array Link to the params array, from which are retrieved all the data.
     * @return string html page
     * @throws \Exception
     */
    public function output($params)
    {
        ob_start();
        $tasks    = $params['tasks'];
        $messages = $params['messages'];
        $queries  = $params['queries'];
        //параметры для навбара-логина
        $authorized = $params['authorized'];
        $isAdmin = $params['is_admin'];
        $usernameDisplayed = $params['username'];
        
        if ($tasks === false) {
            $messages[] = 'Результат: ничего не найдено.';
        } else {
            foreach ($tasks as $task) {
                $array = $task->getArray();
                //вручную меняем php-значения на их текстовый вид
                if ($array['fulfilled'] === false) {
                    $array['fulfilled'] = 'Не выполнено';
                } else {
                    $array['fulfilled'] = 'Выполнено';
                }
                //наконец, добавляем массив в список задач
                $content[] = $array;
            }
            $tasks = $content;
        }
       
        //загружаем шаблон, который использует вышеописанные переменные
        $template = $this->twig->load('list.html.twig');
        echo $template->render(array(
            'tasks'    => $tasks,
            'messages' => $messages,
            'queries'  => $queries,
            'authorized' => $authorized,
            'is_admin' => $isAdmin,
            'username'   => $usernameDisplayed
        ));
        return ob_get_clean();
    }
}