<?php

namespace MusicPlayer\Controller;

use MusicPlayer\Config;

/**
 * Base Controller
 *
 * @author Adrian Pennington <adrian@ajpennington.net>
 */
abstract class Controller {
	/**
	 * @var StdClass $request
	 */
	protected $request;

	/**
 	 * @var StdClass $response;
 	 */
	protected $response;

	public function __construct() {
		$this->request = (object) $_REQUEST;
	}

	/**
	 * Sets the response for the controller
	 * 
	 * @param mixed $response
	 * @return self
	 */
	public function set_response($response) {
		$this->response = $response;

		return $this;
	}

	/**
	 * Return response from controller
	 */
	public function response() {
		return $this->response;
	}

	/**
	 * Check HTTP method matches requested
	 * @param string $method HTTP method get, post, head, put, delete, options etc
	 */
	protected function using_http_method($method) {
		return (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === strtoupper($method));
	}

	public function redirect($page) {
		header('Location: /' . Config::get('base_url') . '?page=' . $page);
	}
}