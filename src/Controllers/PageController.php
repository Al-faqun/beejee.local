<?php
namespace BeeJee\Controllers;



class PageController
{
    protected $requests;
    protected $messages;
    
    protected function __construct()
    {
        $messages = array();
    }
    
    function get($key, callable $call)
    {
        $this->requests[] = new Request($_GET, $key, $call, $this);
    }
    
    function post($key, callable $call)
    {
        $this->requests[] = new Request($_POST, $key, $call, $this);
    }
    
    function cookie($key, callable $call)
    {
        $this->requests[] = new Request($_COOKIE, $key, $call, $this);
    }
    
    function noGet($key, callable $call)
    {
        $this->requests[] = new Request($_GET, $key, $call, $this, true);
    }
    
    function noPost($key, callable $call)
    {
        $this->requests[] = new Request($_POST, $key, $call, $this, true);
    }
    
    function noCookie($key, callable $call)
    {
        $this->requests[] = new Request($_COOKIE, $key, $call, $this, true);
    }
    
    function execute()
    {
        if (!empty($this->requests) AND is_array($this->requests))
            foreach ($this->requests as $request) {
                $request->call();
            }
    }
    
    function redirect($address)
    {
        header('Location: ' . $address, true, 303);
        exit();
    }
    
    public function addMessage(String $message)
    {
        $this->messages[] = $message;
    }
    
    public function getMessages()
    {
        return $this->messages;
    }
}
