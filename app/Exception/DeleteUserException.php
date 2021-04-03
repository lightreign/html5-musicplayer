<?php

namespace MusicPlayer\Exception;

use Exception;

/**
 * Cannot delete user
 * 
 * @author Adrian Pennington <adrian@penningtonfamily.net>
 */
class DeleteUserException extends Exception {
    protected $username;

    public function __construct($username) {
        $this->username = $username;
        $this->message = $username . ' could not be deleted from the system, it may be the only remaining user left';
    }
}