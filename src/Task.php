<?php

namespace BeeJee;

class Task
{
    private $id;
    private $userid;
    private $username;
    private $email;
    private $text;
    private $imgPathRel;
    private $fulfilled;
    
    public function __construct($id, $userid, $username, $email, $text, $imgPathRel, $fulfilled)
    {
        $this->setId($id);
        $this->setUserid($userid);
        $this->setUsername($username);
        $this->setEmail($email);
        $this->setText($text);
        $this->setImgPathRel($imgPathRel);
        $this->setFulfilled($fulfilled);
    }
    
    /**
     * @param int $id
     */
    public function setId($id)
    {
        if (is_int($id)) {
            $this->id = $id;
        } else {
            throw new \Exception('Task id is not int');
        }
    }
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param mixed $userid
     */
    public function setUserid($userid)
    {
        if (is_int($userid)) {
            $this->userid = $userid;
        } else {
            throw new \Exception('User id for task is not int');
        }
    }
    
    /**
     * @return mixed
     */
    public function getUserid()
    {
        return $this->userid;
    }
    
    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
    
    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * @param mixed $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }
    
    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }
    
    /**
     * @param mixed $imgPathRel
     */
    public function setImgPathRel($imgPathRel)
    {
        $this->imgPathRel = $imgPathRel;
    }
    
    /**
     * @return mixed
     */
    public function getImgPathRel()
    {
        return $this->imgPathRel;
    }
    
    /**
     * @param boolean $fulfilled
     */
    public function setFulfilled($fulfilled)
    {
        if (is_bool($fulfilled)) {
            $this->fulfilled = $fulfilled;
        } else {
            throw new \Exception('Task status is not bool');
        }
    }
    
    /**
     * @return boolean
     */
    public function getFulfilled()
    {
        return $this->fulfilled;
    }
    
    /**
     * @return array
     */
    public function getArray()
    {
        $result = array(
            'id'         => $this->getId(),
            'username'   => $this->getUsername(),
            'email'      => $this->getEmail(),
            'text'       => $this->getText(),
            'imgPathRel' => $this->getImgPathRel(),
            'fulfilled'  => $this->getFulfilled()
        );
        return $result;
    }
}