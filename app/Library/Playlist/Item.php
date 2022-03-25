<?php

namespace MusicPlayer\Library\Playlist;

use MusicPlayer\Model;

class Item extends Model {
	protected $table = 'playlistItems';

    protected $id_field = 'itemID';

    /**
     * Item Id
     *
     * @var int
     */
    protected $id;

    /**
     * File path
     * @var string
     */
    protected $file;

}