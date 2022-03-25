<?php

namespace MusicPlayer;

/**
 * Enforce singleton pattern on object and provide instance
 * 
 * @author  Adrian Pennington <git@penningtonfamily.net>
 */
abstract class Singleton {
    protected static $instances = [];

    final protected function __construct() {
        $this->init();
    }

    protected function init() {
    }

    /**
     * Get singleton instance
     */
    final public static function get_instance() {
        if (empty(static::$instances[static::class])) {
            static::$instances[static::class] = new static();
        }
        
        return static::$instances[static::class];
    }
}