<?php

namespace MusicPlayer\Exception;

use Exception;

/**
 * Authentication failed
 *
 * @author Adrian Pennington <git@penningtonfamily.net>
 */
class AuthenticationFailed extends Exception {
    public function __construct($username) {
        $this->message = 'Invalid username or password combination';
    }
}
