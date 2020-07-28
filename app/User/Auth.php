<?php

namespace MusicPlayer\User;

use MusicPlayer\Config;
use MusicPlayer\Database;
use MusicPlayer\Exception\AuthenticationFailed;

/**
 * User Authentication logic
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
final class Auth {
    use Database;

    public function __construct() {
        $this->connect();
    }

    /**
     * Check if a user is authenticated
     */
    public static function is_authenticated() {
        $requires_auth = Config::get('auth');

        if ($requires_auth === false) {
            return true;
        } elseif ($requires_auth === true && !empty($_SESSION['username'])) {
            return true;
        } elseif ($requires_auth === 'remote_only' && $_SERVER['SERVER_NAME'] === 'localhost') {
            return true;
        }

        return false;
    }

    /**
     * Check if autentication is required/enabled
     */
    public static function is_auth_enabled() {
        $requires_auth = Config::get('auth');
        
       if ($requires_auth === true) {
            return true;
        } elseif ($requires_auth === 'remote_only' && $_SERVER['SERVER_NAME'] !== 'localhost') {
            return true;
        }

        return false;
    }

    /**
     * @param string $username
     * @param string $password
     * @return true User if authenticated
     * @throws AuthenticationFailed if user is not authenticated
     */
    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password', User::encrypt_password($password));
        $result = $stmt->execute();

        if ($firstRow = $result->fetchArray(SQLITE3_ASSOC)) {
            $user = new User($firstRow);

            return $this->authenticated($user);
        }

        throw new AuthenticationFailed($username);
    }

    private function authenticated(User $user) {
        $_SESSION['username'] = $user->username();
        $_SESSION['accept'] = 1;

        return true;
    }

    public function unauthenticate() {
        $_SESSION['username'] = null;
        $_SESSION['accept'] = 0;

        return true;
    }
}
