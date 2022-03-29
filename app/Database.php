<?php

namespace MusicPlayer;

use MusicPlayer\Exception\DatabaseException;
use SQLite3;

/**
 * Database Trait
 *
 * @author Adrian Pennington <git@penningtonfamily.net>
 */
Trait Database {
    /**
     * @var SQLite3 $db
     */
    protected $db;

    /**
     * @param string $sqlite_file
     */
    public function connect($sqlite_file = null) {
        $this->db = new SQLite3($sqlite_file ?? Config::get_database_file());
        $this->db->enableExceptions(true);

        $this->db->exec('PRAGMA foreign_keys = ON');
    }
}
