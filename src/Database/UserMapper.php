<?php
namespace BeeJee\Database;


use BeeJee\User;

class UserMapper
{
    private $pdo;
    
    /**
     * UserMapper constructor.
     * @param \PDO $pdo
     */
    function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    /**
     * @param $id
     * @return User|bool
     * @throws \Exception
     */
    function getUser($id)
    {
        try {
            $sql = 'SELECT `name` FROM `beejee_users` WHERE `id` = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result !== false) {
                $assoc = $stmt->fetch(\PDO::FETCH_ASSOC);
                $result = new User($id, $assoc['name']);
            }
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при получении пользователя.', 0, $e);
        }
        return $result;
    }
    
    /**
     * @param $name
     * @return User|bool
     * @throws \Exception
     */
    function getUserByName($name)
    {
        try {
            $sql = 'SELECT `id`, `name` FROM `beejee_users` WHERE `name` = :name';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name, \PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result !== false) {
                $assoc = $stmt->fetch(\PDO::FETCH_ASSOC);
                $result = new User($assoc['id'], $assoc['name']);
            }
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при получении пользователя.', 0, $e);
        }
        return $result;
    }
    
    /**
     * @param $name
     * @return string|bool
     * @throws \Exception
     */
    function getIdFromName($name)
    {
        try {
            $sql = 'SELECT `id` FROM `beejee_users` WHERE `name` = :name';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $result = $stmt->execute();
            if ($result !== false) {
                $assoc = $stmt->fetch(\PDO::FETCH_ASSOC);
                $result = $assoc['id'];
            }
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при получении ID пользователя.', 0, $e);
        }
        return $result;
    }
    
    /**
     * @param $name
     * @return User|bool
     * @throws \Exception
     */
    function addUser($name, $hash)
    {
        try {
            $sql = 'INSERT INTO `beejee_users`(`name`, `hash`) VALUES (:name, :hash)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':hash', $hash);
            $result = $stmt->execute();
            if ( ($result !== false) AND ($this->lastInsertedId() !== 0) ) {
                $id = $this->lastInsertedId();
                $result = $this->getUser($id);
            }
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при добавлении пользователя.', 0, $e);
        }
        return $result;
    }
    
    /**
     * Имеет смысл использовать только следующим же выражением после insert.
     *
     * @return int id of last inserted ID or 0 if cannot retrieve
     */
    public function lastInsertedId()
    {
        return (int)$this->pdo->lastInsertId();
    }
    
    public function doesExist($username)
    {
        try {
            $sql = 'SELECT `id` FROM `beejee_users` WHERE `name` = :name';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $username);
            $result = $stmt->execute();
            if ($result !== false) {
                $assoc = $stmt->fetch(\PDO::FETCH_ASSOC);
                $result = empty($assoc) ? false : true;
            }
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при получении пользователя.', 0, $e);
        }
        return $result;
    }
    /**
     * @param $userID
     * @return bool|string
     * @throws \Exception
     */
    function getHashFromUser($userID)
    {
        try {
            $sql = 'SELECT `hash` FROM `beejee_users` WHERE `id` = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userID, \PDO::PARAM_INT);
            $result = $stmt->execute();
            if ($result !== false) {
                $assoc = $stmt->fetch(\PDO::FETCH_ASSOC);
                if (array_key_exists('hash', $assoc)) {
                    $result = $assoc['hash'];
                } else $result = false;
            }
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при получении пользователя.', 0, $e);
        }
        return $result;
    }
    
    /**
     * @param $username
     * @return bool
     * @throws \Exception
     */
    function getHashByName($username)
    {
        try {
            $sql = 'SELECT `hash` FROM `beejee_users` WHERE `name` = :name';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $username);
            $result = $stmt->execute();
            if ($result !== false) {
                $assoc = $stmt->fetch(\PDO::FETCH_ASSOC);
                if (array_key_exists('hash', $assoc)) {
                    $result = $assoc['hash'];
                } else $result = false;
            }
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при получении пользователя.', 0, $e);
        }
        return $result;
    }
    
    /**
     * @param $username
     * @param $newHash
     * @return bool
     * @throws \Exception
     */
    function changeHashForUser($username, $newHash)
    {
        try {
            $sql = 'UPDATE `beejee_users` SET `hash` = :hash WHERE `name` = :name';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':name', $username);
            $stmt->bindParam(':hash', $newHash);
            $result = $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при изменении хэша пользователя.', 0, $e);
        }
        return $result;
    }
    
    /**
     * @param $userID
     * @return bool
     * @throws \Exception
     */
    function deleteUser($userID)
    {
        try {
            $sql = 'DELETE FROM `beejee_users` WHERE `id` = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $userID, \PDO::PARAM_INT);
            $result = $stmt->execute();
        } catch (\PDOException $e) {
            throw new \Exception('Ошибка при удалении пользователя.', 0, $e);
        }
        return $result;
    }
}