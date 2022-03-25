<?php

namespace MusicPlayer\Library;

use MusicPlayer\Model;

class Playlist extends Model {
	protected $table = 'playlist';

    protected $id_field = 'playlistID';

    /**
     * Playlist Id
     *
     * @var int
     */
    protected $id;

    /**
     * Playlist name
     *
     * @var string
     */
    protected $name;

 	/**
     * Playlist description
     *
     * @var string
     */
    protected $description;

    /**
     * User that owns the playlist
     * @var int
     */
    protected $user_id;

    /**
     * User constructor
     *
     * @param array|int User Id array or id number
     */
    public function __construct($playlist = null) {
        parent::__construct();

        if (is_numeric($playlist)) {
            $playlist = $this->load($playlist);
            error_log(var_export($playlist,true));
        }

        if (is_array($playlist)) {
            $this->id = $playlist['playlistID'];
            $this->name = $playlist['name'];
            $this->description = $playlist['description'];
            $this->user_id = $playlist['userID'];
        }
    }

    /**
     * Get playlist Id
     */
    public function id() {
        return $this->id;
    }

    /**
     * Get playlist name
     *
     * @return string
     */
    public function name() {
        return $this->name;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function description() {
        return $this->description;
    }

    /**
     * Sets playlist name
     *
     * @param string $name
     * @return static
     */
    public function set_name($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Updates a users password and encrypts it
     *
     * @param string $password
     * @return static
     */
    public function set_description($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get playlist user id
     *
     * @return string
     */
    public function user_id() {
        return $this->user_id;
    }

    /**
     * Sets user id
     *
     * @param string $name
     * @return static
     */
    public function set_user_id($user_id) {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Delete the playlist
     *
     * @return int
     * @throws DatabaseException on error
     */
    public function remove() {
        if (!$this->id) {
            return false;
        }

        return $this->delete($this->id);
    }

    /**
     * Save the playlist
     *
     * @return User
     * @throws DatabaseException on error
     */
    public function save() {
        if ($this->id) {
            $this->update(
                $this->id,
                [
                    'name' => $this->name,
                    'description' => $this->description,
                    'userID' => $this->user_id
                ]
            );

        } else {
            $this->id = $this->insert([
                'name' => $this->name,
                'description' => $this->description,
                'userID' => $this->user_id
            ]);
        }

        return $this;
    }

    /**
     * @param int $user_id
     * @return Playlist[] user playlists
     */
    public function user_playlists($user_id) {
        $playlists = $this->select([ '*' ], [ 'userID' => $user_id ]);

        $user_playlists = [];

        foreach ($playlists as $playlist) {
            $user_playlists[] = new static($playlist);
        }

        return $user_playlists;
    }
}