<?php

namespace MusicPlayer;

use Exception;
use SQLite3;

/**
 * Database Trait
 *
 * @author  Adrian Pennington <adrian@ajpennington.net>
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
        try {
            $this->db = new SQLite3($sqlite_file ?? Config::get_database_file());
        } catch (Exception $e) {
            $this->handle_exception($e->getMessage());
        }
    }

    /**
     * @param string $error_msg
     */
    protected function handle_exception($exception) {
        if ($exception instanceof Exception) {
            $error_msg = $exception->getMessage();
        } else {
            $error_msg = $exception;
        }

        if (Console::is_console()) {
            print Console::print($error_msg);
        } else {
            print json_encode(array("status" => "Error", "error" => $error_msg));
        }
    }

    /**
     * @return string DB Last error message
     */
    public function getErrorMessage() {
        return SQLite3::lastErrorMsg;
    }
}
