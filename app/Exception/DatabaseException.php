<?php

namespace MusicPLayer\Exception;

use Exception;
use MusicPLayer\Config;
use SQLite3;

/**
 * Database Error
 * 
 * @author Adrian Pennington <adrian@penningtonfamily.net>
 */
class DatabaseException extends Exception {
    protected $db;

    public function __construct(SQLite3 $db, $message = null) {
        $this->db = $db;
        $this->message = $this->generateMessage($message);
    }

    /**
     * Generate our lovely exception message
     * 
     * @param string $message
     * @return string
     */
    protected function generateMessage($message) {
        if (Config::get('testing') && $this->db->lastErrorMsg()) {
            $message .= ': ' . $this->db->lastErrorMsg();
        } elseif (!$message) {
            $message = 'A database error occurred, please verify the database is setup.';
        }

        return $message;
    }
}