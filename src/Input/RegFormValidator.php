<?php
namespace BeeJee\Input;


use BeeJee\Database\UserMapper;

class RegFormValidator
{
    private $userMapper;
    
    public function __construct(UserMapper $userMapper)
    {
        $this->userMapper = $userMapper;
    }
    
    /**
     * Отослана ли форма
     * @param $input
     * @return bool
     */
    public function dataSent($input)
    {
        $result = false;
        $fieldname = 'reg_form_sent';
        //если есть нужный hidden input
        
        if (array_key_exists($fieldname, $input)
             &&
            ($input[$fieldname] == 1)
        ) {
            $result = true;
        } else $result = false;
        
        return $result;
    }
    
    /**
     * @param $input
     * @return array|bool
     */
    public function checkInput($input, &$errors)
    {
        $result = true;
        $password = $this->checkPassword($input);
        $userName = $this->checkUsername($input);
        $exists = $this->doesExist($userName);
        if ($password === false) {
            $errors[] = 'Введен некорректный пароль. Исправьте!';
            $result = false;
        }
        if ($userName === false) {
            $errors[] = 'Введенное имя недопустимо. Исправьте!';
            $result = false;
        }
        if ($exists === true) {
            $errors[] = 'Пользователь с таким именем уже существует.';
            $result = false;
        }
        if ($result !== false) {
            $result = ['username' => $userName, 'password' => $password];
        }
        
        return $result;
    }
    
    private function checkPassword($input)
    {
        $result = false;
        $fieldname = 'pwd';
        if (array_key_exists($fieldname, $input)
        ) {
            $result = self::checkString($input[$fieldname], 3, 30);
        } else $result = false;
    
        return $result;
    }
    
    private function checkUsername($input)
    {
        $result = false;
        $fieldname = 'username';
        if (array_key_exists($fieldname, $input)
        ) {
            $result = self::checkString($input[$fieldname], 5, 255, true, true, true);
        } else $result = false;
    
        return $result;
    }
    
    /**
     * @param string $userName
     * @return bool
     */
    private function doesExist($userName)
    {
        return $this->userMapper->doesExist($userName);
    }
    
    /**
     * Checks input to be string, optionally to consist of letters, numbers and '_' sign.
     * @param string $string String to check
     * @param int $minlen Minimal permitted length of string to pass check.
     * @param int $maxlen Maximal permitted length of string to pass check.
     * @param bool $trimWhiteSpaces
     * @param bool $onlyLetters
     * @param bool $startsWithLetter Optional parameter is used,
     * when first meaningful symbol of string (except any white character) must be letter.
     * @return bool|string Returns string if it passes test, else FALSE
     * (be careful, any whitespace character in the begginning and the end are deleted).
     */
    private static function checkString(
        $string,
        $minlen,
        $maxlen,
        $trimWhiteSpaces = false,
        $onlyLetters = false,
        $startsWithLetter = false
    )
    {
        if (is_string($string)) {
            //убираем белые символы, если включена опция
            if ($trimWhiteSpaces === true) {
                $string = trim($string);
            }
            //проверяем входные числа
            if (!is_int($minlen) || !is_int($maxlen)) {
                throw new \UnexpectedValueException('Length of string must be integer');
            }
            //проверяем длину строки
            if ( (mb_strlen($string) >= $minlen
                    &&
                  mb_strlen($string) <= $maxlen)
            ) {
                $result = $string;
            } else $result = false;
            
            //дополнительные условия
            if ($onlyLetters === true )
            {
                if (!preg_match('/^\w+$/iu', $string) > 0) {
                    $result = false;
                }
            }
            if ($startsWithLetter === true && !self::startsWithLetter($string)) {
                $result = false;
            }
        } else $result = false;
        
        return $result;
    }
    
    /**
     * Checks whether text variable starts with unicode Letter.
     * @param string $var Variable to test.
     * @return bool TRUE if var starts with letter (case insensitive), else FALSE.
     */
    private static function startsWithLetter($var)
    {
        if ( preg_match('/^\p{L}/iu', $var) ) {
            return true;
        } else return false;
    }
}