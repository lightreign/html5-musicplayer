<?
/**
 * AJAX handler file
 *
 * @author  Adrian Pennington <adrian@penningtonfamily.net>
 */

require_once __DIR__ . '/../bootstrap.php';

use MusicPlayer\Config;

$controller = new \MusicPlayer\Controller\Update;

try {
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

        if (isset($_POST['add_user'])) {
            $controller->add_user();
        }

        if (isset($_POST['rm_user'])) {
            $controller->remove_user();
        }
    }

    print json_encode($controller->response());

} catch (Exception $e) {
    if (Config::get('testing')) {
        print json_encode(['status' => 'Error', 'message' => $e->getMessage() . ' in ' . $e->getFile()  . ':' .  $e->getLine()]);
    } else {
        print json_encode(['status' => 'Error', 'message' => $e->getMessage()]);
    }
}
