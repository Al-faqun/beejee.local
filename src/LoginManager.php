<?php
namespace BeeJee;


use BeeJee\Database\LoginMapper;
use BeeJee\Database\UserMapper;

class LoginManager
{

    private $pdo;
    private $mapper;
    private $input;
    /**
     * @var bool VERY important variable, which tells outside world about,
     * whether 'user' is logged OR it's some stranger.
     */
    private $islogged = false;
    /**
     * @var int Holder for id of user, if exists.
     */
    private $id = 0;
    
    /**
     * LoginManager constructor.
     * @param UserMapper $mapper Mapper that can store hashes in DB.
     * @param array $inputArray Input, containing 'password' and 'user data' of current user of site.
     */
    function __construct(UserMapper $mapper, $pdo)
    {
        $this->mapper = $mapper;
        $this->pdo = $pdo;
    }
    
    /**
     * This method adds User to database.
     *
     * Be careful, this method cannot check whether ID is valid and non-duplicate, so do it yourself.
     * @param string $username
     * @param string $password
     */
    function registerUser($username, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $user = $this->mapper->addUser($username, $hash);
        return $user;
    }
    
    /**
     * Checks depending on provided input, whether user has valid credentials
     * and therefore may be provided with access to system.
     *
     * It also sets internal state of LoginManager to 'logged' or 'not logged'.
     * @return int|bool ID of user if he has valid credentials, or FALSE if not.
     */
    function checkLoginForm($input)
    {
        //проверяем наличие данных для логина
        if  (array_key_exists('login_form_sent', $input)
            AND
             array_key_exists('navbar_username', $input)
            AND
             array_key_exists('navbar_pwd', $input)
        ) {
            //фильтруем их
            $password = (string)$input['navbar_pwd'];
            $name = (string)$input['navbar_username'];
            $hash = $this->mapper->getHashByName($name);
            //проверяем полученный хеш (если ошибка, вместо него false)
            if ($hash !== false) {
                //проверка совпадения
                if (password_verify($password, $hash)) {
                    //если пользователь дал корректные данные, получаем его ID и возвращаем
                    $result = $this->mapper->getUserByName($name)->getId();
                } else {
                    $result = false;
                }
                //проверка, не обновился ли стандартный способ хэширования в php
                if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $this->mapper->changeHashForUser($name, $hash);
                }
            } else {
                $result = false;
            }
        } else {
            $result = false;
        }
        
        return $result;
        
    }
    
    /**
     * @param $userid
     */
    function persistLogin($userid)
    {
        $loginMapper = new LoginMapper($this->pdo);
        //случайный токен для хранения в куках
        $token = self::genRandString(24);
        //его хеш для хранения в бд
        $tokenHash = hash('sha256', $token);
        //идентификатором пользователя будет ID записи логина в бд
        $id = $loginMapper->addLogin($tokenHash, $userid);
        //если запись в бд прошла успешно, записываем данные о логине в куки
        if ($id != false) {
            setcookie('login_id', $id, time()+60*60*24, null, null, null, true);
            setcookie('token', $token,   time()+60*60*24, null, null, null, true);
        }
    }
    
    function logout()
    {
        setcookie('login_id', 0, time()-60*60*24, null, null, null, true);
        setcookie('token', 0,   time()-60*60*24, null, null, null, true);
    }
    
    /**
     * By default it returns FALSE.
     * @return bool TRUE if user is valid and logged, otherwise FALSE.
     */
    function isLogged()
    {
        //проверяем наличие в куки данных
        if  (array_key_exists('login_id', $_COOKIE) AND is_string($_COOKIE['login_id']) //проверка на массив
            AND
            array_key_exists('token', $_COOKIE) AND is_string($_COOKIE['token'])
        ) {
            $loginID = (int)$_COOKIE['login_id'];
            $token = (string)$_COOKIE['token'];
            $loginMapper = new LoginMapper($this->pdo);
            //проверяем наличие id (серии) токена в бд
            $hash = $loginMapper->getHash($loginID);
            //делаем что-то только если хэш  найден в базе
            if ($hash != false) {
                //если хэши из бд и куки совпали
                if (hash_equals($hash, hash('sha256', $token))) {
                    //пользователь обладает нужными данными - он залогинен
                    $this->islogged = true;
                } else {
                    //пользователь дал нужный айди, но провалил проверку пароля => воровство
                    $this->islogged = false;
                }
                
            } else $this->islogged = false;
            
        } else $this->islogged = false;
        
        return $this->islogged;
    }
    
    /**
     * @return bool
     */
    function isAdmin()
    {
        $result = false;
        if ( $this->isLogged() ) {
            //если залогинены, то в куки есть айди
            $loginID = $_COOKIE['login_id'];
            //вызываем мапперы для доступа к бд
            $loginMapper = new LoginMapper($this->pdo);
            $userMapper = $this->mapper;
            //получем из записи о логине айди пользователя
            $userid = $loginMapper->getUserID($loginID);
            //получем его имя
            $username = $userMapper->getUser($userid)->getName();
            //сравниваем
            if ($username === 'admin') {
                $result = true;
            } else $result = false;
        } else $result = false;
        
        return $result;
    }
    
    /**
     * @return int ID of user, if it's credentials were checked, otherwise false.
     */
    function getLoggedID()
    {
        $userid = false;
        if ( $this->isLogged() ) {
            //если залогинены, то в куки есть айди
            $loginID = $_COOKIE['login_id'];
            //вызываем мапперы для доступа к бд
            $loginMapper = new LoginMapper($this->pdo);
            $userMapper = $this->mapper;
            //получем из записи о логине айди пользователя
            $userid = $loginMapper->getUserID($loginID);
        }
        return $userid;
    }
    
    /**
     * False, если юзер не залогинен
     * @return bool|string
     */
    function getLoggedName()
    {
        $username = false;
        if ( $this->isLogged() ) {
            //если залогинены, то в куки есть айди
            $loginID = $_COOKIE['login_id'];
            //вызываем мапперы для доступа к бд
            $loginMapper = new LoginMapper($this->pdo);
            $userMapper = $this->mapper;
            //получем из записи о логине айди пользователя
            $userid = $loginMapper->getUserID($loginID);
            //получем его имя
            $username = $userMapper->getUser($userid)->getName();
        }
        return $username;
    }
    
    /**
     * Generates cryptographically secure string of given length.
     * @param int $length Length of desired random string.
     * @param string $chars Only these characters may be included into string.
     * @return string
     * @throws \Exception
     */
    private static function genRandString($length, $chars='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ+/')
    {
        if (!is_string($chars) || strlen($chars) == 0) {
            throw new \Exception('Parameter is not string or is empty');
        }
        
        $str = '';
        $keysize = strlen($chars) -1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $chars[random_int(0, $keysize)];
        }
        return $str;
    }
}