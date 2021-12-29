<?php

namespace MusicPlayer\Library;

use Exception;
use MusicPlayer\Exception\DatabaseException;
use MusicPlayer\Model;

/**
 * Library
 *
 * @author  Adrian Pennington <adrian@penningtonfamily.net>
 */
class Library extends Model {
    protected $table = 'library';

    protected $id_field = 'libraryID';

    /** @var array $directories */
    protected $directories;
    /** @var File[] $files */
    protected $files;

    /**
     * Constructor
     */ 
    public function __construct($sqlite_file = null) {
        parent::__construct();

        $this->update_directories();

        foreach ($this->directories() as $dir) {
            $this->files = $this->get_files($dir['directory']);
        }
    }

    /**
     * Populate files for directory
     *
     * @param string $dir
     * @return array List of files with filepath
     */
    protected function get_files($dir) {
        $files = [];

        foreach (glob("$dir/*") as $filepath) {
            if (is_dir($filepath) && $filepath !== $dir) {
                $files = array_merge($files, $this->get_files($filepath));

            } else {
                $files[] = new File($filepath);
            }
        }

        return $files;
    }

    /**
     * Return file list
     * 
     * @return File[]
     */
    public function files() {
        return $this->files;
    }

    public function search($term) {
        $regex = '/' . preg_quote($term) . '/i';

        if (empty($term)) {
            return $this->files();
        }

        return array_values(array_filter($this->files(), function(File $file) use ($regex) {
            return preg_match($regex, $file->get_filename());
        }));
    }

    /**
     * Return directory list
     * 
     * @return string[]
     */
    public function directories() {
        return $this->directories;
    }

    /**
     * Get library directories from database
     * 
     * @return array
     */
    protected function retrieve_directories() {
        return $this->select();
    }

    protected function update_directories() {
        $this->directories = $this->retrieve_directories();
    }

    /**
     * Checks if a directory is valid
     *
     * @param string $dir Directory path
     * @return bool
     */
    public function is_valid_dir($dir) {
        if (!is_dir($dir)) {
            return false;
        }

        if (stristr($dir, BASE_DIR) && !stristr($dir, BASE_DIR . 'files')) {
            return false;
        }

        return true;
    }

    /**
     * Creates a library directory
     *
     * @param string $path
     * @return int|false Library dir Id or false if failed
     */
    public function add_directory($path) {
        // Add trailing slash if not exists
        $path = preg_replace('/([^\/])$/', '\\1/', $path);

        try {
            return $this->insert([ 'directory' => $path ]);
        } catch (Exception $e) {
            throw new DatabaseException($this->db, 'Cannot create library directory, maybe it already exists');
        }
    }

    /**
     * Removes a library directory
     * 
     * @param int $id
     * @return bool
     */
    public function remove_directory($id) {
        return $this->delete($id);
    }
}
