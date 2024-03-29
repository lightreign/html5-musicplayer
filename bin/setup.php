#!/usr/bin/env php
<?php

/**
 * Setup script
 * 
 * @author Adrian Pennington <git@penningtonfamily.net>
 */

define('BASE_DIR', realpath(__DIR__ . '/..') . '/');

require_once BASE_DIR . '/vendor/autoload.php';

use MusicPlayer\Config;
use MusicPlayer\Console;

// Check that we are running in the shell
if (php_sapi_name() !== 'cli') {
    die('you can only run this the installer via the command line');
}

// If no config exists copy the sample file
if (!file_exists(BASE_DIR . 'config.yaml')) {
    copy(BASE_DIR . 'config.sample.yaml', BASE_DIR . 'config.yaml');

    Console::print('Setting up default configuration file');
}

$database_dir = BASE_DIR . Config::get('db.dir');
$database_file = $database_dir . Config::get('db.file');

$templates_cache_dir = BASE_DIR . 'templates/cache';

$play_dir = Config::get('play.dir');
$files_dir = Config::get('files.dir');

$force_flag = array_search('-f', $argv);

// Make sure our database file exists
if (!file_exists($database_file) || $force_flag) {
    if ($force_flag) {
        Console::print('Force refreshing database on request, sir');
    }

    if (!is_dir($database_dir)) {
        mkdir($database_dir);
        chmod($database_dir, 0755);

        Console::print('Creating database directory');
    }

    file_put_contents($database_file, '');
    chmod($database_file, 0666);

    try {
        $setup = new \MusicPlayer\Library\Setup();
        $setup->setup_database();
    } catch (Exception $e) {
        die($e->getMessage());
    }

    Console::print('Database has been setup');
} else {
    Console::print('Database already exists.. skipping');
}

// We dont need this when running local server, but for a standalone server we might
if (!is_dir($templates_cache_dir)) {
    mkdir($templates_cache_dir);
    chmod($templates_cache_dir, 0777);

    Console::print('Templates cache directory has been setup');
} else {
    Console::print('Template cache directory already exists.. skipping');
}

// Make sure our play dir exists (and is writable??)
if (!is_dir($play_dir)) {
    mkdir($play_dir);
    chmod($play_dir, 0777);

    Console::print('Play directory has been setup');
} else {
    Console::print('Play directory already exists.. skipping');
}

// Make sure our files dir exists, this is for direct copying of files to a publicly visible directory
// Not ideal but easy to use if your site is only exposed locally
if (!is_dir($files_dir)) {
    mkdir($files_dir);

    Console::print('Music files directory has been setup');
} else {
    Console::print('Music files directory already exists.. skipping');
}

if (!Config::get('salt')) {
    Config::set('salt', bin2hex(random_bytes(15)));

    Console::print('Added a little salt for password security');
} else {
    Console::print('Password salt already applied');
}
