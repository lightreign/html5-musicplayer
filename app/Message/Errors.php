<?php

namespace MusicPlayer\Message;

use MusicPlayer\Singleton;

/**
 * Manage Error messages
 * 
 * @author Adrian Pennington <adrian@ajpennington.net>
 */
class Errors extends Singleton {
	private $errors = [];

	protected function init() {
		if (!empty($_SESSION['error_flash'])) {
			$this->errors = $_SESSION['error_flash'];

			unset($_SESSION['error_flash']);
		}
	}

	/**
	 * Add error to list
	 * 
	 * @param string $error Error message
	 */
	public static function add($error) {
		self::get_instance()->errors[] = $error;
	}

	/**
	 * Add error to list
	 * 
	 * @param string $error Error message
	 */
	public static function addFlash($error) {
		if (!isset($_SESSION['error_flash'])) $_SESSION['error_flash'] = [];

		$_SESSION['error_flash'][] = $error;
	}

	/**
	 * Returns list of errors
	 * 
	 * @return array
	 */
	public static function get() {
		return self::get_instance()->errors;
	}
}