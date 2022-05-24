/*
    Music JS file
    author:  Adrian Pennington
*/

var audioplayer = document.getElementById('player');
var playlists;
var orig_playinglist;
var shuffle = false;

if (audioplayer) {
    audioplayer.onended = function() {
        next_song();
    }    
}

// Media key listeners
navigator.mediaSession.setActionHandler('previoustrack', function() {
    prev_song();
});

navigator.mediaSession.setActionHandler('nexttrack', function() {
    next_song();
});

navigator.mediaSession.setActionHandler('play', function() {
    play();
});

navigator.mediaSession.setActionHandler('pause', function() {
    pause();
});


(function() {
    if ($('#controls').length) {
        playlists().then(function() { search() });
    }

    $('.playlist').on('click', '.music-file:not(.unsupported)', function() {
        $('.item-playing').removeClass('item-playing');
        $(this).addClass('item-playing');

        $('#play').addClass('playing');
        $('#play .label-play').hide();
        $('#play .label-pause').show();
        play_music($(this));
    });

    $('#play').on('click', function() {
        $(this).toggleClass("playing");

        if ($(this).hasClass("playing")) {
            play();
        } else {
           pause();
        }
    });

    $('#shuffle').on('click', function() {
        shuffle = !shuffle;

        $(this).toggleClass('active', shuffle);
        toggle_shuffle_playinglist(shuffle);
    });

    $('#search').on('keyup', _.debounce(search, 500));

    $('#playback').slider({
        value: 0,
        orientation: "horizontal",
        range: "min",
        animate: true,
        start: function() {
            audioplayer._seeking = true;
        },
        stop: function( event, ui ) {
            audioplayer.currentTime = ui.value;
            audioplayer._seeking = false;
        },
    });

    var sliderIcon = $('.slider-icon');

    $('#volume-slider').slider({
        value: audioplayer ? audioplayer.volume * 100 : 0,
        orientation: "horizontal",
        range: "min",
        animate: true,
        stop: function( event, ui ) {
            audioplayer.volume = ui.value / 100;

            update_volume_slider_icon(sliderIcon, audioplayer.volume);
        },
    });

    $('#playlists').on('change', function() {
        $(this).find('option:selected').each(function() {
            open_playlist($(this).val());
        });
    });
})();

function play() {
    var playbtn = $('#play');

    $(playbtn).find('.label-play').hide();
    $(playbtn).find('.label-pause').show();

    $('audio#player').attr('src') == '' ? play_first_song() : audioplayer.play();
}

function pause() {
    var playbtn = $('#play');

    playbtn.find('.label-pause').hide();
    playbtn.find('.label-play').show();
    audioplayer.pause();
    navigator.mediaSession.playbackState = "paused";
}

function play_first_song() {
    $('.music-file:not(.unsupported):first').click();
}

function prev_song() {
    var prevfile = $('td.item-playing').parent().prev().children('td');

    if (prevfile.length) {
        $('.item-playing').removeClass('item-playing');
        prevfile.addClass('item-playing');

        play_music(prevfile);
    }
}

function next_song() {
    var nextfile = $('td.item-playing').parent().next().children('td');

    if (nextfile.length) {
        $('.item-playing').removeClass('item-playing');
        nextfile.addClass('item-playing');

        play_music(nextfile);
    } else {
        play_first_song();
    }
}
function search() {
    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "search=" + $('#search').val(),
        dataType: 'json',
        success: function(json) {
            if (json["status"] == "Error") {
                handle_error(json["message"]);

            } else if (json.status == "Success") {
                var tbody = $('.playlist').find('table>tbody');
                tbody.empty();

                $.each(json.files, function(index, file) {
                    var td = $('<td>').addClass('music-file');
                    var filenameDisplay = $('<span>').addClass('filename');

                    if (!file.playback_supported) {
                        td.addClass('unsupported');
                        filenameDisplay.attr('title', 'File is not playable');
                    }

                    tbody.append(
                        $('<tr>').append(
                            td.append(
                                filenameDisplay.text(file.filename)
                            ).append(
                                $('<span>')
                                    .addClass('file-path')
                                    .addClass('hidden')
                                    .text(file.filepath)
                            )
                        )
                    );
                });
            } else {
                handle_error("No Response from Music Server");
            }
        },
        error: function(x,t,m) {
            handle_error(m);
        },
        complete: function() {
            create_playlist_contextmenu();
        }
    });
}

function playlists() {
    return $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "playlists=1",
        dataType: 'json',
        success: function(response) {
            playlists = response.playlists;
            repopulate_playlist_menu();
        },
        error: function(x,t,m) {
            handle_error(m);
        }
    });
}

function create_playlist_contextmenu() {
    $.contextMenu({
        selector: '.music-file:not(.unsupported)',
        build: function($trigger, e) {
            var items = {};

            playlists.forEach(function(playlist) {
                items[playlist.id] = {
                    name: 'Add to ' + playlist.name, callback: (key, opt) => add_to_playlist(key, opt.$trigger)
                };
            });

            if (_.isEmpty(items)) {
                items.new = {
                    name: 'Create new playlist', callback: () => $('#playlist-modal').modal('show')
                };
            }

            return {
                items: items
            };
        }
    });
}

function repopulate_playlist_menu() {
    $('#playlists')
        .empty()
        .append($('<option>').val('').text('All Music'));

    playlists.forEach(function(playlist) {
        var option = $('<option>').val(playlist.id).text(playlist.name);
        $('#playlists').append(option);
    });
}

function open_playlist(id) {
    if (id === '') {
        search();
        return;
    }

    $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "playlist=" + id,
        dataType: 'json',
        success: function(response) {
            if (response.status == "Error") {
                handle_error(response.message);

            } else if (response.status == "Success") {
                var tbody = $('.playlist').find('table>tbody');
                tbody.empty();

                $.each(response.files, function(index, file) {
                    var td = $('<td>').addClass('music-file');
                    var filenameDisplay = $('<span>');

                    tbody.append(
                        $('<tr>').append(
                            td.append(
                                filenameDisplay.text(file.filename)
                            ).append(
                                $('<span>')
                                    .addClass('file-path')
                                    .addClass('hidden')
                                    .text(file.filepath)
                            )
                        )
                    );
                });
            } else {
                handle_error("No Response from Music Server");
            }
        },
        error: function(x,t,m) {
            handle_error(m);
        }
    });
}

function add_to_playlist(id, element) {
    var filepath = $(element).find('span.file-path').text();

    return $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "add_to_playlist=" + id + '&filepath=' + filepath,
        dataType: 'json',
        success: function(response) {
            if (response.status == "Error") {
                handle_error('Unable to add song to playlist, might already exist');
            } else {
                handle_success('Song added to playlist');
            }
        },
        error: function(x,t,m) {
            handle_error(m);
        }
    });
}

function play_music(object) {
    if (object.hasClass('unsupported')) {
        next_song();
        return;
    }

    $('.error_msg, .success_msg').addClass('hidden');
    navigator.mediaSession.playbackState = "playing";

    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: "play=" + object.find('span.file-path').text(),
        dataType: 'json',
        success: function(json) {
            if (json["status"] == "Error") {
                handle_error(json["message"]);

            } else if (json["status"] == "Success") {
                $('audio#player').attr('src', json["file"]).attr('autoplay',true);
                navigator.mediaSession.playbackState = "playing";

                $('title').text(object.find('> span.filename').text());
                $('#playback').show();

            } else {
                handle_error("No Response from Music Server");
            }
        },
        error: function(x,t,m) {
            handle_error(m);
        }
    });
}

function volume_up() {
    var player = document.getElementById('player');

    if ((player.volume + 0.1) > 1.0) {
        return;
    }

    player.volume += 0.1;
}

function volume_down() {
    var player = document.getElementById('player');

    if (player.volume <= 0.1) {
        player.volume = 0;
        return;
    }

    player.volume -= 0.1;
}

function update_duration(audio) {
    $("#duration").text(convert_to_time(audio.currentTime) + " / " + convert_to_time(audio.duration));

    if (audio._seeking) {
        return;
    }

    update_playback_slider(audio.currentTime, audio.duration);
}

function convert_to_time(seconds) {
    if (isNaN(seconds)) {
        return '00:00';
    }

    seconds = Math.floor(seconds);
    var minutes = Math.floor(seconds / 60);

    minutes = minutes >= 10 ? minutes : '0' + minutes;

    seconds = Math.floor( seconds % 60 );
    seconds = seconds >= 10 ? seconds : '0' + seconds;

    return minutes + ':' + seconds;
}

function update_playback_slider(currentTime, duration) {
    $( "#playback" ).slider({
        value: currentTime,
        orientation: "horizontal",
        range: "min",
        min: 0,
        max: duration,
        animate: true,
    });
}

function update_volume_slider_icon(icon, volume) {
    if (volume <= 0) {
        icon
            .removeClass('glyphicon-volume-up')
            .removeClass('glyphicon-volume-down')
            .addClass('glyphicon-volume-off');
    } else if (volume <= 0.5) {
        icon
            .removeClass('glyphicon-volume-up')
            .removeClass('glyphicon-volume-off')
            .addClass('glyphicon-volume-down');
    } else {
        icon
            .removeClass('glyphicon-volume-down')
            .removeClass('glyphicon-volume-off')
            .addClass('glyphicon-volume-up');
    }
}

function toggle_unplayable() {
    $('.playlist .unsupported').toggleClass('hidden');
}

function toggle_shuffle_playinglist(shuffle) {
    if (!shuffle) {
        $('.playlist table tbody').empty().append(orig_playinglist);
    } else {
        orig_playinglist = $('.playlist table tbody tr').detach();
        const playinglist = Object.assign(orig_playinglist);

        $(_.shuffle($.makeArray(playinglist))).appendTo($('.playlist table tbody'));
    }

}


