<?php

namespace MusicPlayer\Library;

use MusicPlayer\Config;
use MusicPlayer\Exception\FileNotFoundException;

/**
 * File class
 *
 * @author Adrian Pennington <adrian@penningtonfamily.net>
 */
class File {
    protected $filename;
    protected $extension;
    protected $path;
    protected $playdir;
    protected $playurl;
    protected $playfile;

    protected $supported_formats = ['mp3', 'm4a','wav', 'ogg'];

    public function __construct($filepath) {
        if (!file_exists($filepath)) {
            throw new FileNotFoundException($filepath);
        }

        $this->filepath = $filepath;
        $this->filename = basename($filepath);
        $this->path =  dirname($filepath);
        $this->extension = pathinfo($filepath, PATHINFO_EXTENSION);

        $this->playdir = BASE_DIR . Config::get('play.dir');
        $this->playurl = Config::get('base_url') . Config::get('play.url');
    }

    /**
     * Grab just the plain source filename
     *
     * @return string filename
     */
    public function get_filename() {
        return $this->filename;
    }

    /**
     * Get the source filename and its whole path
     * 
     * @return string file path
     */
    public function get_file_with_path() {
        return $this->filepath;
    }

    /**
     * Get the temp symlinked file url so the browser can play it
     * 
     * @return string relative url
     */
    public function get_playurl() {
        return $this->playurl . $this->filename;
    }

    /**
     * HTML5 Audio tag only supports a few formats, check this here
     *
     * @return bool
     */
    public function format_supported() {
        return in_array(strtolower($this->extension), $this->supported_formats);
    }

    /**
     * Create a symlink to play dir so file can be played
     * Could emit warning which is converted to exception if cannot symlink file
     *
     * @return string
     */
    public function symlink_to_playdir() {
        // Clear out old symlinks
        array_map('unlink', glob("{$this->playdir}/*"));

        $link = "{$this->playdir}/{$this->filename}";

        // Create new symlink
        symlink($this->filepath, $link);

        $this->playfile = $link;

        return readlink($link);
    }
}
