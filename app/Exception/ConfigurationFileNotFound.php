<?php

namespace MusicPlayer\Exception;

/**
 * File was not found on disk
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
class ConfigurationFileNotFound extends FileNotFoundException {
	public function __construct($filename) {
		parent::__construct($filename);

		$this->message = 'Configuration file: ' . basename($filename) .  ' not found on the filesystem';
	}
}