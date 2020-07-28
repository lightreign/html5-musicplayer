<?php

namespace MusicPlayer;

use ErrorException;

/**
 * Error Handler class
 * Converts any warnings/errors to exceptions
 * 
 * @author Adrian Pennington <adrian@ajpennington.net>
 * @link https://www.php.net/manual/en/class.errorexception.php
 */
class ErrorHandler {
	public static function handle($severity, $message, $file, $line) {
		if (!(error_reporting() & $severity)) {
        	// This error code is not included in error_reporting
        	return;
	    }

	    throw new ErrorException($message, 0, $severity, $file, $line);
	}
}
