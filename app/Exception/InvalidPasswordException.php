<?php

namespace MusicPlayer\Exception;

use Exception;

/**
 * Password does not meet required strength
 * 
 * @author Adrian Pennington <adrian@penningtonfamily.net>
 */
class InvalidPasswordException extends Exception {
     public function __construct() {
        $this->message = 'Password is invalid, it must be at least 4 characters long';
    }
}