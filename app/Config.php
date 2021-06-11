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

        // Check config file is not empty
        if (!$this->config->get('db')) {
            throw new ConfigurationFileNotFound(realpath(self::$config_file));
        }
    }

    /**
     * Check if configuration exists
     *
     * @return bool true if exists,false if otherwise
     */
    public static function exists() {
        try {
            self::get_instance();
        } catch (ConfigurationFileNotFound $e) {
            return false;
        }

        return true;
    }

    /**
     * Wraps around Config object, gets value by key
     *
     * @param string key
     * @return mixed config value
     */
    public static function get($key) {
        return self::get_instance()->config->get($key);
    }

    /**
     * Wraps around Config object, sets value by key
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public static function set($key, $value) {
        self::get_instance()->config->set($key, $value);
        self::get_instance()->config->toFile(self::$config_file);
    }

    /**
     * Shortcut method cos otherwise its pretty long winded!
     *
     * @return string full path to database
     */
    public static function get_database_file() {
        return BASE_DIR . self::get_instance()->config->get('db.dir') . self::get_instance()->config->get('db.file');
    }
}
