<?php

namespace MusicPlayer\Library;

use MusicPlayer\Config;
use MusicPlayer\Database;
use MusicPlayer\Library\Library;
use MusicPlayer\User\User;

/**
 * Setup Database
 *
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
class Setup {
    use Database;

    /**
     * Setup constructor
     */
    public function __construct() {
        $this->connect();
    }

    /**
     * Setup Database
     * 
     * @return bool Setup completed successfully
     * @throws DatabaseException on error
     */
    public function setup_database() {
        $this->setup_schema();
        $this->setup_files_dir();

        $user = new User();
        $user->set_username('music')
            ->set_password('music')
            ->save();

        return true;
    }

    /**
     * Create the db schema
     *
     * @return void
     */
    protected function setup_schema() {
        $this->db->exec('CREATE TABLE library (libraryID INTEGER PRIMARY KEY, directory TEXT UNIQUE)');
        $this->db->exec('CREATE TABLE playlist (playlistID INTEGER PRIMARY KEY, playlist TEXT)');
        $this->db->exec('CREATE TABLE playlistItems (itemID INTEGER PRIMARY KEY, playlistID INT, item TEXT)');
        $this->db->exec('CREATE TABLE users (userID INTEGER PRIMARY KEY, username TEXT UNIQUE, password TEXT)');
    }

    /**
     * Add the default files/ dir to the list
     *
     * @throws FilePermissionsException
     */
    protected function setup_files_dir() {
        $files_dir = BASE_DIR . Config::get('files.dir');
        $library = new Library;

        if (is_dir($files_dir)) {
           $library->insert(['directory' => $files_dir]);
        }
    }
}
