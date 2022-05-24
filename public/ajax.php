<?php
/**
 * AJAX handler file
 *
 * @author  Adrian Pennington <git@penningtonfamily.net>
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

        if (isset($_GET['search'])) {
            $controller->search();
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

        if (isset($_GET['playlists'])) {
            $controller->playlists();
        }

        if (isset($_GET['playlist'])) {
            $controller->playlist();
        }

        if (isset($_POST['add_to_playlist'])) {
            $controller->add_to_playlist();
        }

        if (isset($_POST['add_playlist'])) {
            $controller->add_playlist();
        }

        if (isset($_POST['rm_playlist'])) {
            $controller->remove_playlist();
        }

        if (isset($_POST['update_settings'])) {
            $controller->update_settings();
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
