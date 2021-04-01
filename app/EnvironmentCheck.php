<?php

namespace MusicPlayer;

/**
 * Check the environment
 * @author  Adrian Pennington <adrian@penningtonfamily.net>
 */
class EnvironmentCheck {
    /**
     * Is database directory writable
     */
    public static function database_dir_writable() {
        return is_writable(BASE_DIR . Config::get('db.dir'));
    }

    /**
     * Does database file exist
     */
    public static function database_file_exists() {
        return file_exists(Config::get_database_file());
    }

    /**
     * Does database file exist
     */
    public static function play_dir_writable() {
        return is_writable(BASE_DIR . Config::get('play.dir'));
    }

    /**
     * Check if environment is sane
     * 
     * @return bool
     */
    public static function is_ok() {
        return self::database_dir_writable() && self::database_file_exists() && self::play_dir_writable();
    }
}