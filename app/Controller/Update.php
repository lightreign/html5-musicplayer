<?php

namespace MusicPlayer\Controller;

use Exception;
use MusicPlayer\Exception\FileNotFoundException;
use MusicPlayer\Exception\FilePermissionException;
use MusicPlayer\Library\File;
use MusicPlayer\Library\Library;
use MusicPlayer\Library\Playlist;
use MusicPlayer\Library\Playlist\Item;
use MusicPlayer\User\Auth;
use MusicPlayer\User\User;
use MusicPlayer\User\Users;

/**
 * Update controller
 *
 * @author Adrian Pennington <git@penningtonfamily.net>
 */
class Update extends Controller {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();

        // Set default response
        $this->response = (object) [
            "message" =>  null,
            "status" =>  "Route not found",
        ];

        $this->library = new Library();
    }

    /**
     * Play a music file
     */
    public function play() {
        try {
            $file = new File($this->request->play);
            $file->symlink_to_playdir();

            $this->response->status   = "Success";
            $this->response->file     = $file->get_playurl();

        } catch (FileNotFoundException $e) {
            $this->response->message = "file not present on disk";
            $this->response->status = "Error";
        } catch (FilePermssionException $e) {
            $this->response->message = "failure to play due to write permissions in 'play' directory";
            $this->response->status = "Error";
        }
    }

    /**
     * Filter music list by search
     */
    public function search() {
        $this->response->status = "Success";
        $this->response->files = $this->library->search($this->request->search ?? '');
    }

    /**
     * Add a directory
     */
    public function add_directory() {
        $dir = $this->request->add_directory;

        if ($this->library->is_valid_dir($dir)) {
            $this->response->message = $this->library->add_directory($dir);
            $this->response->status  = "Success";

        } else {
            $this->response->message = "directory '$dir' not found on server";
            $this->response->status = "Error";
        }
    }

    /**
     * Remove a directory
     */
    public function remove_directory() {
        $dir = $this->request->rm_directory;

        if ($this->library->remove_directory($dir)) {
            $this->response->status   = "Success";

        } else {
            $this->response->message = "directory $dir not removed from library";
            $this->response->status = "Error";
        }
    }

    /**
     * Add a user
     */
    public function add_user() {
        $username = $this->request->add_user;
        $password = $this->request->password;

        try {
            $user = new User();
            $user->set_username($username)
                ->set_password($password)
                ->save();

            $this->response->userid = $user->id();
            $this->response->username = $user->username();
            $this->response->status = "Success";
            $this->response->message = "User {$user->username()} created";

        } catch (Exception $e) {
            $this->response->status = "Error";
            $this->response->message = $e->getMessage();
        }
    }

    /**
     * Remove a user
     */
    public function remove_user() {
        $user_id = $this->request->rm_user;

        try {
            $user = new User($user_id);
            $user->remove();

            $this->response->user_id = $user_id;
            $this->response->username = $user->username();
            $this->response->status = "Success";
            $this->response->message = "User removed";

        } catch (Exception $e) {
            $this->response->status = "Error";
            $this->response->message = $e->getMessage();
        }
    }

    public function settings() {
        $current_user = Auth::get_authenticated_user();
        $user = new User($current_user['id']);
        
        $this->response = $user->settings();
    }

    public function update_settings() {
        $current_user = Auth::get_authenticated_user();
        unset($this->request->update_settings);

        $settings = $this->request;

        try {
            $user = new User($current_user['id']);
            $user->set_settings($settings)
                ->save();

            $this->response->status = "Success";
            $this->response->message = "Settings updated";

        } catch (Exception $e) {
            $this->response->status = "Error";
            $this->response->message = $e->getMessage();
        }

    }

    /**
     * List playlists
     */
    public function playlists() {
        try {
            $current_user = Auth::get_authenticated_user();
            $user = new User($current_user['id']);

            $this->response->playlists = $user->playlists();
            $this->response->status = "Success";
            $this->response->message = "";

        } catch (Exception $e) {
            $this->response->status = "Error";
            $this->response->message = $e->getMessage();
        }
    }

    /**
     * get playlist items
     */
    public function playlist() {
        try {
            $current_user = Auth::get_authenticated_user();
            $user = new User($current_user['id']);
            $playlist = new Playlist($this->request->playlist);

            $this->response->files = $playlist->items();
            $this->response->status = "Success";
            $this->response->message = "";

        } catch (Exception $e) {
            $this->response->status = "Error";
            $this->response->message = $e->getMessage();
        }
    }

    /**
     * Add to a playlist
     */
    public function add_to_playlist() {
        $playlist_id = $this->request->add_to_playlist;
        $filepath = $this->request->filepath;

        try {
            $playlist_item = new Item();
            $playlist_item->set_playlist_id($playlist_id)
                ->set_filepath($filepath)
                ->save();

            $this->response->itemid = $playlist_item->id();
            $this->response->playlistid = $playlist_item->playlist_id();
            $this->response->filepath = $playlist_item->filepath();
            $this->response->status = "Success";
            $this->response->message = "Item added to playlist";

        } catch (Exception $e) {
            $this->response->status = "Error";
            $this->response->message = $e->getMessage();
        }
    }

    /**
     * Add a playlist
     */
    public function add_playlist() {
        $name = $this->request->add_playlist;
        $description = $this->request->description;

        try {
            $playlist = new Playlist();
            $playlist->set_name($name)
                ->set_description($description)
                ->set_user_id(Auth::get_authenticated_user()['id'])
                ->save();

            $this->response->id = $playlist->id();
            $this->response->name = $playlist->name();
            $this->response->description = $playlist->description();
            $this->response->user_id = $playlist->user_id();
            $this->response->status = "Success";
            $this->response->message = "Playlist {$playlist->name()} created";

        } catch (Exception $e) {
            $this->response->status = "Error";
            $this->response->message = $e->getMessage();
        }
    }

    /**
     * Remove a playlist
     */
    public function remove_playlist() {
        $playlist_id = $this->request->rm_playlist;

        try {
            $playlist = new Playlist($playlist_id);

            if ($playlist->user_id() !== Auth::get_authenticated_user()['id']) {
                throw new Exception('Permission Denied');
            }

            $playlist->remove();

            $this->response->playlistid = $playlist->id();
            $this->response->name = $playlist->name();
            $this->response->description = $playlist->description();
            $this->response->user_id = $playlist->user_id();
            $this->response->status = "Success";
            $this->response->message = "Playlist removed";

        } catch (Exception $e) {
            $this->response->status = "Error";
            $this->response->message = $e->getMessage();
        }
    }

    /**
     * Deny access
     */
    public function access_denied() {
        $this->response->status = "Error";
        $this->response->message = "You are not authorised to perform this action";
    }
}