<?php

namespace MusicPlayer;

/**
 * Interfacing with console utils class
 * 
 * @author  Adrian Pennington <git@penningtonfamily.net>
 */
class Console {
    public static function is_console() {
        return php_sapi_name() === 'cli';
    }

    public static function print($message) {
        print $message . PHP_EOL;
    }
}
