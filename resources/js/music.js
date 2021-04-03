/*
    Music JS file
    author:  Adrian Pennington
*/

var audioplayer = document.getElementById('player');

if (audioplayer) {
    audioplayer.onended = function() {
        next_song();
    }    
}

(function() {
    $("#js-test").removeClass("glyphicon-remove").addClass("glyphicon-ok");

    if ($('#js-test').hasClass('glyphicon-remove')) {
        $('#install').attr('disabled', true);
    }

    $('.music-file:not(.unsupported').on('click', function() {
        $('.item-playing').removeClass('item-playing');
        $(this).addClass('item-playing');

        $('#play').addClass('playing');
        $('#play').text("Pause");

        play_music($(this));
    });

    $('#play').on('click', function() {
        $(this).toggleClass("playing");

        if ($(this).hasClass("playing")) {
            $(this).text("Pause");

            $('audio#player').attr('src') == '' ? $('.music-file:not(.unsupported):first').click() : audioplayer.play();

        } else {
            $(this).text("Play");
            audioplayer.pause();
        }
    });

    $('#add-library').on('click', function() {
        if (!$('#dirpath').val()) {
            handle_error("No directory path specified");
            return;
        }

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: "add_directory=" + $('#dirpath').val(),
            dataType: 'json',
            success: function(json) {
                if (json["status"] == "Error") {
                    handle_error(json["message"]);

                } else if (json["status"] == "Success") {
                    handle_success('Directory added successfully');

                    $('.library-table').append("<tr id='lib" + json['message'] +"'>" + 
                        "<td>" + $('#dirpath').val() + "</td>" +
                        "<td><a href='#' class='rmlibrary' libraryID='" + json['message'] +
                        "'><span class='glyphicon glyphicon-remove' title='Delete From Library'></span></a></td>"
                    );

                } else {
                    
                    handle_error("No Response from Music Server");
                }
            },
            error: function(x,t,m) {
                handle_error(m);
            },
            complete: function() {
                $('#dirpath').val('');
            }
        });
    });

    $('.library-table tbody').on('click', 'a.rmlibrary', function() {
        var libraryID = $(this).attr('libraryID');

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: "rm_directory=" + libraryID,
            dataType: 'json',
            success: function(json) {
                if (json["status"] == "Error") {
                    handle_error(json["message"]);

                } else if (json["status"] == "Success") {
                    handle_success('Directory removed successfully');

                    $("tr#lib" + libraryID).remove();

                } else {
                    handle_error("No Response from Music Server");
                }
            },
            error: function(x,t,m) {
                handle_error(m);
            }
        });
    });

    $('#add-user').on('click', function() {
        var username = $('#username').val();
        var password = $('#password').val();

        if (!username || !password) {
            handle_error("You must specify a username and password to create a user");
            return;
        }

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: "add_user=" + username + '&password=' + password,
            dataType: 'json',
            success: function(json) {
                if (json.status == "Error") {
                    handle_error(json.message);

                } else if (json.status == "Success") {
                    handle_success('User added successfully');

                    $(".user-table tbody").append('<tr id="user' + json.userid +'">' +
                        '<td>' + json.username + '</td><td>***************</td>' +
                        '<td><a href="#" class="rmuser" user-id="'+ json.userid +'"><span class="glyphicon glyphicon-remove" title="Delete User"></span></a> ' +
                        // '<a href="#" class="edituser" user-id="'+ json.userid +'"><span class="glyphicon glyphicon-pencil" title="Edit user"></span></a>' +
                        '</td></tr>'
                    );

                } else {
                    handle_error("No Response from Music Server");
                }
            },
            error: function(x,t,m) {
                handle_error(m);
            },
            complete: function() {
                $('#username').val('');
                $('#password').val('');
            }
        });
    });

    $('.user-table tbody').on('click', 'a.rmuser', function() {
        var userID = $(this).attr('userId');

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: "rm_user=" + userID,
            dataType: 'json',
            success: function(json) {
                if (json["status"] == "Error") {
                    handle_error(json.message);

                } else if (json.status== "Success") {
                    handle_success('User ' + json.username + ' removed successfully');

                    $("tr#user" + userID).remove();

                } else {
                    handle_error("No Response from Music Server");
                }
            },
            error: function(x,t,m) {
                handle_error(m);
            }
        });
    });
})();


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
    }
}

function play_music(object) {
    if (object.hasClass('unsupported')) {
        return;
    }

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

            } else {
                handle_error("No Response from Music Server");
            }
        },
        error: function(x,t,m) {
            handle_error(m);
        }
    });
};

function update_duration(audio) {
    $("#duration").text(convert_to_time(audio.currentTime) + " / " + convert_to_time(audio.duration));
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

function handle_error(error_msg) {
    $(".error_msg .message").text(error_msg);
    $(".error_msg").removeClass('hidden');
}

function handle_success(success_msg) {
    $(".success_msg .message").text(success_msg);
    $(".success_msg").removeClass('hidden');
}
