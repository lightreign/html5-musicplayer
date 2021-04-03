<?php

namespace MusicPlayer\Exception;

use Exception;

/**
 * Configuration not in correct format or invalid
 * 
 * @author Adrian Pennington <adrian@penningtonfamily.net>
 */
class ConfigurationError extends Exception {
    public function __construct($config_key = null) {
        $this->message = 'Configuration error: ' . ($config_key ?? '');
    }
}