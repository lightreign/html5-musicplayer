/*
    Settings Page JS file
    author:  Adrian Pennington
*/

(function() {
    $('#dirpath').on('keyup', function() {
        disable_enable_button_text_length($('#add-library'), $(this));
    });

    $('#library-form').on('submit', function(e) {
        e.preventDefault();

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
                if (json.status == "Error") {
                    handle_error(json.message);

                } else if (json.status == "Success") {
                    handle_success('Directory added successfully');

                    $('.library-table').append("<tr id='lib" + json.message +"'>" + 
                        "<td>" + $('#dirpath').val() + "</td>" +
                        "<td><a href='#' class='rmlibrary' libraryID='" + json.message +
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

    $('#username').on('keyup', function() {
        $('#add-user').prop('disabled', !($(this).val().length && $('#password').val().length));
    });

    $('#password').on('keyup', function() {
        $('#add-user').prop('disabled', !($(this).val().length && $('#username').val().length));
    });

    $('#user-form').on('submit', function(e) {
        e.preventDefault();

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
