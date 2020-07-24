<?
/**
 * AJAX handler file
 *
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */

require_once __DIR__ . '/../bootstrap.php';

$controller = new \MusicPlayer\Controller\Update;

if (!MusicPlayer\User\Auth::is_authenticated()) {
	$controller->access_denied();

} else {
	if (isset($_POST['play'])) {
	    $controller->play();
	}

	if (isset($_POST['add_directory'])) {
	    $controller->add_directory();
	}

	if (isset($_POST['rm_directory'])) {
	    $controller->remove_directory();
	}
}

print json_encode($controller->response());