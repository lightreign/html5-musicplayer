<?php

namespace MusicPlayer\Message;

use MusicPlayer\Singleton;

/**
 * Manage Success messages
 * 
 * @author Adrian Pennington <adrian@penningtonfamily.net>
 */
class Success extends Singleton {
    private $messages = [];

    protected function init() {
        if (!empty($_SESSION['success_flash'])) {
            $this->messages = $_SESSION['success_flash'];

            unset($_SESSION['success_flash']);
        }
    }

    /**
     * Add messages to list
     * 
     * @param string $message Message
     */
    public static function add($message) {
        self::get_instance()->messages[] = $message;
    }

    /**
     * Add messages to list
     * 
     * @param string $message Message
     */
    public static function addFlash($message) {
        if (!isset($_SESSION['success_flash'])) $_SESSION['success_flash'] = [];

        $_SESSION['success_flash'][] = $message;
    }

    /**
     * Returns list of success messages
     * 
     * @return array
     */
    public static function get() {
        return self::get_instance()->messages;
    }
}