<?php

namespace MusicPlayer\User;

use MusicPlayer\Database;

/**
 * Class for interacting with users in bulk
 *
 * @author Adrian Pennington <adrian@penningtonfamily.net>
 */
class Users {
    use Database;

    protected $table = 'users';

    protected $id_field = 'userID';

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

    /**
     * Get total user count
     *
     * @return int
     */
    public function count() {
        $query = "SELECT COUNT(*) FROM users";

        $stmt = $this->db->query($query);

        return $stmt->fetchArray(SQLITE3_NUM)[0];
    }
}