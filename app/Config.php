<?php

namespace MusicPlayer;

use MusicPlayer\Exception\ConfigurationError;
use MusicPlayer\Exception\ConfigurationNotFound;
use MusicPlayer\Exception\ConfigurationFileNotFound;

use Noodlehaus\Config as ConfigReader;
use Noodlehaus\Parser\Yaml;

/**
 * Wrapper Config class
 * 
 * @author  Adrian Pennington <adrian@penningtonfamily.net>
 */
class Config extends Singleton {
    protected $config;

    protected static $config_file = __DIR__ . '/../config.yaml';

    /**
     * Populate config
     */
    protected function init() {
        if (!file_exists(self::$config_file)) {
            throw new ConfigurationFileNotFound(realpath(self::$config_file));
        }

        $this->config = new ConfigReader(realpath(self::$config_file), new Yaml);
    }

    /**
     * Wraps around ConfigReader, gets value by key
     */
    public static function get($key) {
        return self::get_instance()->config->get($key);
    }

    /**
     * Wraps around ConfigReader, gets value by key
     */
    public static function set($key, $value) {
        self::get_instance()->config->set($key, $value);
        self::get_instance()->config->toFile(self::$config_file);
    }

    /**
     * Shortcut method cos otherwise its pretty long winded!
     */
    public static function get_database_file() {
        return BASE_DIR . self::get_instance()->config->get('db.dir') . self::get_instance()->config->get('db.file');
    }
}
