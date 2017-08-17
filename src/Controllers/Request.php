<?php

namespace BeeJee\Controllers;

/**
 * Class Request encapsulates actions with requesr data (post, get, cookie)
 * @package BeeJee\Controllers
 */
class Request
{
    private $input;
    private $key;
    private $callable;
    private $controller;
    private $invert;
    
    /**
     * Request constructor.
     * @param array $input
     * @param string $key
     * @param callable $call
     * @param $controller
     * @param bool $invert
     */
    function __construct($input, $key, callable $call, $controller, $invert = false)
    {
        $this->input = $input;
        $this->key = $key;
        $this->callable = $call;
        $this->controller = $controller;
        //нужно ли выполнить действие в *отсутствие* нужного ключа
        $this->invert = $invert;
    }
    
    /**
     * Выполнить запланированное действие
     * @return bool
     */
    function call()
    {
        if (!$this->invert) {
            if ( array_key_exists($this->key, $this->input) ) {
                return ($this->callable)($this->key, $this->input[$this->key], $this->controller);
            } else return false;
        } else {
            if ( !array_key_exists($this->key, $this->input) ) {
                return ($this->callable)($this->key, null, $this->controller);
            } else return false;
        }
    }
}