<?php

namespace MusicPlayer\Exception;

use Exception;

class AuthenticationFailed extends Exception {
	public function __construct($username) {
		$this->message = 'Invalid username or password combination';
	}
}
