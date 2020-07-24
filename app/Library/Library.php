<?php

namespace MusicPlayer\Library;

use Exception;
use MusicPlayer\Database;

/**
 * Library
 *
 * @author  Adrian Pennington <adrian@ajpennington.net>
 */
class Library {
    use Database;

    /** @var array $directories */
    protected $directories;
    /** @var File[] $files */
    protected $files;

    /**
     * Constructor
     */ 
    public function __construct($sqlite_file = null) {
        $this->connect();
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
     * Files
     * 
     * @return array
     */
    public function files() {
        return $this->files;
    }

    /**
     * @return array
     */
    public function directories() {
        return $this->directories;
    }

    /**
     * @return array
     */
    protected function retrieve_directories() {
        $query = "SELECT * FROM library";

        $stmt = $this->db->query($query);
        $result = [];

        while ($row = $stmt->fetchArray(SQLITE3_ASSOC)) {
            array_push($result, $row);
        }

        return $result;
    }

    protected function update_directories() {
        $this->directories = $this->retrieve_directories();
    }

    /**
     * @param string $path
     * @return int|false Library dir Id or false if failed
     */
    public function add_directory($path) {
        
        try {
            $stmt = $this->db->prepare('INSERT INTO library VALUES (null, :path)');
            $stmt->bindValue(':path', $path);
            $stmt->execute();
        } catch (Exception $e) {
            return false;
        }

        return $this->db->lastInsertRowID();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function remove_directory($id) {

        try {
            $stmt = $this->db->prepare('DELETE FROM library WHERE libraryID = :id');
            $stmt->bindValue(':id', $id);
            $stmt->execute();

        } catch (Exception $e) {
            return false;
        }

        return true;
    }
}
