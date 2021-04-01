<?php

namespace MusicPlayer\Exception;

/**
 * Configuration not in correct format or invalid
 * 
 * @author  Adrian Pennington <adrian@penningtonfamily.net>
 */
class ConfigurationNotFound extends ConfigurationError {
    public function __construct($config_key = null) {
        $this->message = 'Configuration not found: ' . ($config_key ?? '');
    }
}