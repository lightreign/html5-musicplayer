<?php

namespace MusicPlayer\Library\Playlist;

use JsonSerializable;
use MusicPlayer\Model;

class Item extends Model implements JsonSerializable {
	protected $table = 'playlistItems';

    protected $id_field = 'itemID';

    /**
     * Item Id
     *
     * @var int
     */
    protected $id;

    /**
     * Playlist Id
     * @var int
     */
    protected $playlist_id;

    /**
     * File path
     * @var string
     */
    protected $filepath;

    public function __construct($playlist = null) {
        parent::__construct();

        if (is_numeric($playlist)) {
            $playlist = $this->load($playlist);
        }

        if (is_array($playlist)) {
            $this->id = $playlist['itemID'];
            $this->playlist_id = $playlist['playlistID'];
            $this->filepath = strip_tags($playlist['filepath']);
        }
    }

    /**
     * Get item Id
     */
    public function id() {
        return $this->id;
    }

    public function playlist_id() {
        return $this->playlist_id;
    }

    public function filepath() {
        return $this->filepath;
    }

    public function set_playlist_id($playlist_id) {
        $this->playlist_id = $playlist_id;

        return $this;
    }

    public function set_filepath($filepath) {
        $this->filepath = $filepath;

        return $this;
    }

    /**
     * Save the playlist item
     *
     * @return User
     * @throws DatabaseException on error
     */
    public function save() {
        if ($this->id) {
            $this->update(
                $this->id,
                [
                    'playlistID' => $this->playlist_id,
                    'filepath' => $this->filepath
                ]
            );

        } else {
            $this->id = $this->insert([
                'playlistID' => $this->playlist_id,
                'filepath' => $this->filepath
            ]);
        }

        return $this;
    }

    /**
     * Serialised version of object containing important bits
     */
    public function jsonSerialize() {
        return [
            'filename' => basename($this->filepath),
            'filepath' => urlencode($this->filepath),
            'playback_supported' => true,
        ];
    }

    /**
     * @param int $playlist_id
     * @return static[]
     */
    public function playlist_items($playlist_id) {
        $items = $this->select([ '*' ], [ 'playlistID' => $playlist_id ]);

        $playlist_items = [];

        foreach ($items as $item) {
            $playlist_items[] = new static($item);
        }

        return $playlist_items;
    }

}