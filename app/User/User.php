<?php

namespace MusicPlayer\User;

use MusicPlayer\Config;
use MusicPlayer\Model;
use MusicPlayer\Exception\ConfigurationNotFound;
use MusicPlayer\Exception\DatabaseException;

/**
 * User class
 * 
 * @author  Adrian Pennington <adrian@penningtonfamily.net>
 */
class User extends Model {
    protected $table = 'users';

    protected $id_field = 'userID';

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
        $salt = Config::get('salt');

        if (!$salt) {
            throw new ConfigurationNotFound('salt');
        }

        return crypt($password, $salt);
    }

    /**
     * Save the user
     * 
     * @return User
     * @throws DatabaseException on error
     */
    public function save() {
        if ($this->id) {
            $this->update($this->id, ['username' => $this->username, 'password' => $this->password]);

        } else {
            $this->insert(['username' => $this->username, 'password' => $this->password]);
        }

        return $this;
    }

    /**
     * Delete the user
     * 
     * @return User
     * @throws DatabaseException on error
     */
    public function delete_user() {
        if (!$this->id) {
            return false;
        }

        $this->delete($this->id);
    }
}
