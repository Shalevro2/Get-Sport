'use strict';
/**
 * Script of jquery
 * load data of chat every 2 sec (setInterval)
 * On press enter in txtarea the message save in the db
 * The $.post() method loads data from the server using a HTTP POST request.
 */

$(document).ready(function () {
    loadChat();
});

$('#message').keyup(function (e) {
    var message = $(this).val();
    if (e.which == 13) {

        $.post('/Group/sendMessage', {
            message: message
        }, function (response) {
            loadChat();
            $('#message').val(''); //delete the text from textArea
        });
    }
});

/**
 * load the chat details
 */
function loadChat() {
    $.post('/Group/getChat', function (response) {
        $('.chathistory').html(response);
    });
}

setInterval(function () {
    loadChat();
}, 2000);