<?php
/**
 * Main page
 * 
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
require_once __DIR__ . '/../bootstrap.php';

use MusicPlayer\EnvironmentCheck;
use MusicPlayer\User\Auth;

$controller = new \MusicPlayer\Controller\Main;

$page = $_GET['page'] ?? '';

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

print $controller->response();
