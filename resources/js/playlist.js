/*
    Playlist JS file
    author:  Adrian Pennington
*/

(function() {
    $('#playlist-name').on('keyup', function() {
        disable_enable_button_text_length($('#add-playlist'), $(this));
    });

    $('#playlist-form').on('submit', function(e) {
        e.preventDefault();

        var name = $('#playlist-name').val();
        var description = $('#playlist-description').val();

        if (!name) {
            handle_error("You must specify a playlist name");
            return;
        }

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: "add_playlist=" + name + '&description=' + description,
            dataType: 'json',
            success: function(json) {
                if (json.status == "Error") {
                    handle_error(json.message);

                } else if (json.status == "Success") {
                    handle_modal_success('Playlist created');

                    $(".playlist-table tbody").append('<tr id="playlist' + json.id +'">' +
                        '<td>' + json.name + '</td>' +
                        '<td>' + json.description + '</td>' +
                        '<td><a href="#" class="rmplaylist" playlistId="'+ json.id +'"><span class="glyphicon glyphicon-remove" title="Delete Playlist"></span></a> ' +
                        '</td></tr>'
                    );

                    playlists.push(json);
                    repopulate_playlist_menu();

                } else {
                    handle_modal_error("No Response from Music Server");
                }
            },
            error: function(x,t,m) {
                handle_error(m);
            },
            complete: function() {
                $('#playlist-name').val('');
                $('#playlist-description').val('');
            }
        });
    });

    $('.playlist-table tbody').on('click', 'a.rmplaylist', function() {
        var playlistID = $(this).attr('playlistId');

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: "rm_playlist=" + playlistID,
            dataType: 'json',
            success: function(json) {
                if (json["status"] == "Error") {
                    handle_error(json.message);

                } else if (json.status== "Success") {
                    handle_modal_success('Playlist ' + json.name + ' removed');

                    $("tr#playlist" + playlistID).remove();

                    _.remove(playlists, (playlist) => playlist.id === Number(playlistID));
                    repopulate_playlist_menu();

                } else {
                    handle_modal_error("No Response from Music Server");
                }
            },
            error: function(x,t,m) {
                handle_error(m);
            }
        });
    });

    $('#playlist-modal').on('show.bs.modal', function (event) {
        $(this).find('.alert').addClass('hidden');
    });
})();