<?php

namespace MusicPlayer\Controller;

use MusicPlayer\Exception\FileNotFoundException;
use MusicPlayer\Exception\FilePermssionException;
use MusicPlayer\Library\File;
use MusicPlayer\Library\Library;

/**
 * Update controller
 *
 * @author Adrian Pennington <adrian@penningtonfamily.net>
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
            "status" =>  "Error",
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

    public function access_denied() {
        $this->response->status = "Error";
        $this->response->message = "You are not authorised to perform this action";
    }
}