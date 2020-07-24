<?php

namespace MusicPlayer\Exception;

use Exception;

/**
 * File was not found on disk
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
class FileNotFoundException extends Exception {
	protected $filename;

	public function __construct($filename) {
		$this->filename = $filename;
		$this->message = basename($filename) .  ' not found on the filesystem';
	}
}