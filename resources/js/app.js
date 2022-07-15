/*
    General JS file
    author:  Adrian Pennington
*/
let notifications = false;

(async function() {
    $("#js-test").removeClass("glyphicon-remove").addClass("glyphicon-ok");

    if ($('#js-test').hasClass('glyphicon-remove')) {
        $('#install').attr('disabled', true);
    }

    let settingPromise = $.ajax({
        type: "GET",
        url: "ajax.php",
        data: "settings=1",
        dataType: 'json',
        success: (data) => {
            settings = data;
        },
        error: () => {
            handle_error('unable to get user settings');
        }
    });

    try {
        const settings = await settingPromise;
    } catch (error) {
        handle_error('unable to get user settings');
    }

    if (window.Notification && settings.notifications) {
        notifications = true;
    }
})();

function handle_error(error_msg) {
    $("#alert-box .error_msg .message").text(error_msg);
    $("#alert-box .error_msg").removeClass('hidden');
}

function handle_success(success_msg) {
    $("#alert-box .success_msg .message").text(success_msg);
    $("#alert-box .success_msg").removeClass('hidden');
}

function handle_modal_error(error_msg) {
    $(".modal .error_msg .message").text(error_msg);
    $(".modal .error_msg").removeClass('hidden');
}

function handle_modal_success(success_msg) {
    $(".modal .success_msg .message").text(success_msg);
    $(".modal .success_msg").removeClass('hidden');
}

function disable_enable_button_text_length(btn, input) {
    btn.prop('disabled', !(input.val().length));
}
