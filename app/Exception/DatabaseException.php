<?php

namespace MusicPLayer\Exception;

use Exception;

/**
 * Database Error
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
class DatabaseException extends Exception {
	public function __construct($message = null) {
		$this->message = $this->generateMessage($message);
	}

	protected function generateMessage($message) {
		if (SQLite3::lastErrorMsg) {
			return $message . ': ' . SQLite3::lastErrorMsg;
		} elseif (!$message && !SQLite3::changes) {
			return 'DB Operation did not add/modify any rows, was it performed successfully?';
		}

		return $message;
	}
}