<?php
/**
 * Main page
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
require_once __DIR__ . '/../bootstrap.php';

use MusicPlayer\EnvironmentCheck;
use MusicPlayer\Message\Errors;
use MusicPlayer\User\Auth;

$controller = new \MusicPlayer\Controller\Main;

$page = $_GET['page'] ?? '';

try {
	if (!EnvironmentCheck::is_ok()) {
		// Environment issue, tell the user about it
		$controller->check();
	} elseif (!Auth::is_authenticated()) {
		// User not autenticated, send them to login page
		$controller->login();
	} elseif (!empty($page) && method_exists($controller, $page)) {
		$controller->$page();
	} else {
		$controller->index();
	}

} catch (Exception $e) {
	Errors::add($e->getMessage());
	$controller->check();
}

print $controller->response();
