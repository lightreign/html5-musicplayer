<?php

namespace MusicPlayer\User;

use MusicPlayer\Config;
use MusicPlayer\Model;
use MusicPlayer\Library\Playlist;
use MusicPlayer\Exception\ConfigurationNotFound;
use MusicPlayer\Exception\DatabaseException;
use MusicPlayer\Exception\InvalidPasswordException;

/**
 * User class
 *
 * @author Adrian Pennington <git@penningtonfamily.net>
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

    /**
     * User Settings JSON
     * @var string
     */
    protected $settings = '{}';

    /**
     * User constructor
     *
     * @param array|int User Id array or id number
     */
    public function __construct($user = null) {
        parent::__construct();

        if (is_numeric($user)) {
            $user = $this->load($user);
        }

        if (is_array($user)) {
            $this->id = $user['userID'];
            $this->username = $user['username'];
            $this->password = $user['password'];
            $this->settings = $user['settings'];
        }
    }

    /**
     * Get user Id
     */
    public function id() {
        return $this->id;
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
        $this->check_password($password);

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
     * Checks password is valid
     *
     * @param string $password
     * @return bool true if valid
     * @throws InvalidPasswordException if password invalid
     */
    protected function check_password($password) {
        if (strlen($password) < 4) {
            throw new InvalidPasswordException;
        }

        return true;
    }

    /**
     * Save the user
     *
     * @return User
     * @throws DatabaseException on error
     */
    public function save() {
        if ($this->id) {
            $this->update(
                $this->id,
                [
                    'username' => $this->username,
                    'password' => $this->password,
                    'settings' => $this->settings
                ]
            );

        } else {
            $this->id = $this->insert([
                'username' => $this->username,
                'password' => $this->password,
                'settings' => $this->settings
            ]);
        }

        return $this;
    }

    /**
     * Delete the user
     *
     * @return User
     * @throws DatabaseException on error
     */
    public function remove() {
        if (!$this->id) {
            return false;
        }

        $users = new Users;

        if ($users->count() <= 1) {
            throw new DeletedUserException($this->username);
        }

        $this->delete($this->id);
    }

    /**
     * Get settings
     *
     * @return array
     */
    public function settings() {

        $settings = Config::get('settings');
        $user_settings = json_decode($this->settings, true);

        if ($user_settings === null) {
            return $settings;
        }

        return array_replace($settings, $user_settings);
    }

    /**
     * Set settings
     *
     * @param string|array|object $settings
     * @return $this
     */
    public function set_settings($user_settings) {
        $settings = Config::get('settings');

        if (is_string($user_settings)) {
            $user_settings = json_decode($user_settings, true) ?? [];
        } else {
            $user_settings = (array)$user_settings;
        }

        foreach ($settings as $setting => $default_value) {
            if (!isset($user_settings[$setting])) {
                $user_settings[$setting] = '';
            }
        }


        $this->settings = json_encode($user_settings);

        return $this;
    }

    public function playlists() {
        return (new Playlist)->user_playlists($this->id);
    }
}
