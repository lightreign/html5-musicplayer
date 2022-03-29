<?php
/**
 * Init file, sets up things nicely
 * @author  Adrian Pennington <git@penningtonfamily.net>
 */

session_start();

define('BASE_DIR', __DIR__ . '/');

require_once __DIR__ . '/vendor/autoload.php';

use MusicPlayer\Config;
use MusicPlayer\Message\Errors;

set_error_handler(['\MusicPlayer\ErrorHandler', 'handle']);

if (!Config::exists()) {
    die('Configuration file missing, please run: `npm run build` to setup defaults');
}

if (Config::get('testing')) error_reporting(E_ALL);

// Check your environment is sane
if (!function_exists('json_encode')) {
    Errors::add('JSON encode does not exist, please install the php5-json package.');
}

if (file_exists(Config::get_database_file()) && !is_writable(Config::get_database_file())) {
    Errors::add('Database is not writable, you wont be able to update any libraries or files.');
}
