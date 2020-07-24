<?php

namespace MusicPlayer\User;

use MusicPlayer\Database;

/**
 * Class for interacting with users in bulk
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
class Users {
    use Database;

    public function __construct() {
        $this->connect();
    }

    /**
     * Get all users
     * 
     * @return User[]
     */
    public function getAll() {
        $query = "SELECT * FROM users";

        $stmt = $this->db->query($query);

        $result = [];

        while ($row = $stmt->fetchArray(SQLITE3_ASSOC)) {
            $result[] = new User($row);
        }

        return $result;
    }
}