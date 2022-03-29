<?php

namespace MusicPlayer\Library;

use JsonSerializable;
use MusicPlayer\Exception\FilePermissionException;
use MusicPlayer\Model;
use MusicPlayer\User\Auth;

class Playlist extends Model implements JsonSerializable {
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
        }

        if (is_array($playlist)) {
            $this->id = $playlist['playlistID'];
            $this->name = strip_tags($playlist['name']);
            $this->description = strip_tags($playlist['description']);
            $this->user_id = $playlist['userID'];

            $this->user_can_access(Auth::get_authenticated_user());
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
     * @param array $user_to_check
     * @return void
     * @throws FilePermissionException if user cannot access
     */
    public function user_can_access($user_to_check) {
        if ($this->user_id !== $user_to_check['id']) {
            throw new FilePermissionException('Playlist cannot be accessed by this user');
        }
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

    public function add_item(Playlist\Item $item) {
        $item->set_playlist_id($this->id);

        return $item->save();

    }

    public function remove_item(Playlist\Item $item) {
        return $item->delete($item->id);
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
     * Serialised version of object containing important bits
     */
    public function jsonSerialize() {
        return [
            'id' => $this->id(),
            'name' => $this->name(),
            'description' => $this->description(),
            'userID' => $this->user_id()
        ];
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

    /**
     * @param int $user_id
     * @return Playlist[] user playlists
     */
    public function items() {
        return (new Playlist\Item)->playlist_items($this->id);
    }
}