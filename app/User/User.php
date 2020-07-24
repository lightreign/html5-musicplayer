<?php

namespace MusicPlayer\User;

use Exception;
use MusicPlayer\Database;
use MusicPlayer\Exception\DatabaseException;

/**
 * User class
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
class User {
    use Database;

    /**
     * Password Salt
     *
     * @var string
     */
    protected static $salt = '0m';

    /**
     * User Id
     * 
     * @var int
     */
    protected $id;

    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Encrypted password
     * 
     * @var string
     */
    protected $password;

    public function __construct(array $user = null) {
        if (is_array($user)) {
            $this->id = $user['userID'];
            $this->username = $user['username'];
            $this->password = $user['password'];
        }

        $this->connect();
    }

    /**
     * Get username
     * 
     * @return string
     */
    public function username() {
        return $this->username;
    }

    /**
     * Get password
     * 
     * @return string
     */
    public function password() {
        return $this->password;
    }

    /**
     * Sets username
     * 
     * @param string $username
     * @return User
     */
    public function set_username($username) {
        $this->username = $username;

        return $this;
    }

    /**
     * Updates a users password and encrypts it
     *
     * @param string $password
     * @return User
     */
    public function set_password($password) {
        $this->password = self::encrypt_password($password);

        return $this;
    }

    /**
     * Encrypts user password for storage in database
     *
     * @param string $password
     * @return string encrypted password
     */
    public static function encrypt_password($password) {
        return crypt($password, self::$salt);
    }

    /**
     * Save the user
     * 
     * @return User
     * @throws DatabaseException on error
     */
    public function save() {
        if ($this->id) {
            $stmt = $this->db->prepare("UPDATE users SET username = :username, password = :password  where userID = :id");
            $stmt->bindValue(':id', $this->id);
            $stmt->bindValue(':username', $this->username);
            $stmt->bindValue(':password', $this->password);

            if (!$stmt->execute()) {
                throw new DatabaseException;
            }


        } else {
            $stmt = $this->db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
            $stmt->bindValue(':username', $this->username);
            $stmt->bindValue(':password', $this->password);

            if (!$stmt->execute()) {
                throw new DatabaseException;
            }
        }

        return $this;
    }

    /**
     * Delete the user
     * 
     * @return User
     * @throws DatabaseException on error
     */
    public function delete() {
        if (!$this->id) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM users where userID = :id");
        $stmt->bindValue(':id', $this->id);
        
        if (!$stmt->execute()) {
            throw new DatabaseException;
        }
    }
}
