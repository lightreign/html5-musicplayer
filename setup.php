<?php

/**
 * Setup script
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */

define('BASE_DIR', __DIR__ . '/');

require_once __DIR__ . '/vendor/autoload.php';

use MusicPlayer\Config;
use MusicPlayer\Console;

$database_dir = BASE_DIR . Config::get('db.dir');
$database_file = $database_dir . Config::get('db.file');

$templates_cache_dir = BASE_DIR . 'templates/cache';

$play_dir = Config::get('play.dir');
$files_dir = Config::get('files.dir');

// Check that we are running in the shell
if (php_sapi_name() !== 'cli') {
    die('you can only run this the installer via the command line');
}

// Make sure our database file exists
if (!file_exists($database_file) || $force_flag = array_search('-f', $argv)) {
    if ($force_flag) {
        Console::print('Force refreshing database on request, sir');
    }

    if (!is_dir($database_dir)) {
        mkdir($database_dir);
    }

    file_put_contents($database_file, '');
    chmod($database_file, 0666);

    try {
        $setup = new \MusicPlayer\Library\Setup();
        $setup->setup_database();
    } catch (Exception $e) {
        die($e->getMessage());
    }
    
} else {
    Console::print('Database already exists.. skipping');
}

// We dont need this when running local server, but for a standalone server we might
if (!is_dir($templates_cache_dir)) {
    mkdir($templates_cache_dir);
    chmod($templates_cache_dir, 0777);
} else {
    Console::print('Template cache folder already exists.. skipping');
}

// Make sure our play dir exists (and is writable??)
if (!is_dir($play_dir)) {
    mkdir($play_dir);
    chmod($play_dir, 0777);
} else {
    Console::print('Play directory already exists.. skipping');
}

// Make sure our files dir exists, this is for direct copying of files to a publicly visible directory
// Not ideal but easy to use if your site is only exposed locally
if (!is_dir($files_dir)) {
    mkdir($files_dir);
} else {
    Console::print('Music files directory already exists.. skipping');
}
