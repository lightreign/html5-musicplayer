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
    // display background file via javascript so we can resolve paths dynamically
    $('.fullscreen_bg').css('background-image', 'url(' + $('#baseurl').text() + '/assets/themes/' + theme + '/background.jpg)');

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

            $('audio#player').attr('src') == '' ? $('.music-file:first').click() : audioplayer.play();

        } else {
            $(this).text("Play");
            audioplayer.pause();
        }
    });

    $('#addlibrary').on('click', function() {
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
})();


function prev_song() {
    var prevfile = $('td.item-playing').parent().prev().children('td');

    $('.item-playing').removeClass('item-playing');
    prevfile.addClass('item-playing');

    play_music(prevfile);
}

function next_song() {
    var nextfile = $('td.item-playing').parent().next().children('td');

    $('.item-playing').removeClass('item-playing');
    nextfile.addClass('item-playing');

    play_music(nextfile);
}

function play_music(object) {
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
    seconds = Math.floor(seconds);
    var minutes = Math.floor( seconds / 60 );

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
