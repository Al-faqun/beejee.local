<?php
namespace BeeJee;


class User
{
    private $id;
    private $name;
    
    public function __construct($id, $name)
    {
        $this->setId($id);
        $this->setName($name);
    }
    
    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $id = (int)$id;
        if ($id !== 0) {
            $this->id = $id;
        } else {
            throw new \Exception('ID is not integer');
        }
    }
    
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}