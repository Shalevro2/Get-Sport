'use strict';
/////////////////////////////Const///////////////////////////////
const chatHistory = document.getElementById("chathistoryID");


//////////////////////////////Chat///////////////////////////////
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
        scrollDown(); //after send new message scroll down the div
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

/**
 * Scroll down the chat div
 */
function scrollDown() {
    chatHistory.scrollTop = chatHistory.scrollHeight;
}

//////////////////////////////Requsets///////////////////////////////

/**
 * On click button accept add user to group
 */
$(document).on("click", ".btnAccept", function () {
    var userName = $(this).attr('id');
    $(this).parent('p').remove();
    $.post('/Group/acceptRequest', {
        userName: userName
    }, function (response) {
        loadNames();
    });
});

/**
 * load the userName that in group
 */
function loadNames() {
    $.post('/Group/getUsersInGroup', function (response) {
        $('.namesDiv').html(response);
    });
}


/**
 * On click button reject delete request and deny user
 */
$(document).on("click", ".btnReject", function () {
    var userName = $(this).attr('id');
    $(this).parent('p').remove();
    $.post('/Group/rejectRequest', {
        userName: userName
    }, function (response) {});
});


/**
 * load the Requests to join the group
 */
function loadRequests() {
    $('#addUsersDiv').load(' #addUsersDiv');
    $.post('/Group/getRequests', function (response) {
        $('#requestsDiv').html(response);
    });
}

////////////////////////Admin invitations////////////////////////
$(document).on("click", ".btnInvite", function () {
    if ($('#addUsers').val() != "") {
        var userNames = $('#addUsers').val();
        $.post('/Group/inviteUsers', {
            userNames: userNames
        }, function (response) {
            $('#addUsers').html(response);
        });
    }
});

////////////////////////////Exit group///////////////////////////
$('.btnExit').click(function (e) {
    var groupName = $(this).attr('id');
    if (confirm("Do you want to get out of the group?")) {

        $.post('/Group/exitGroup', {
            groupName: groupName
        }, function (response) {
            location.replace(response)
        });
    }
});

////////////////////////////Delete group///////////////////////////
$('.btnDeleteGroup').click(function (e) {
    var groupName = $(this).attr('id');

    if (confirm("Do you want to delete the group?")) {

        $.post('/Group/deleteGroup', {
            groupName: groupName
        }, function (response) {
            location.replace(response)
        });
    }
});

////////////////////////////Remove user///////////////////////////
/**
 * On click button 'btnRemoveUser' remove user from group
 */
$(document).on("click", ".btnRemoveUser", function () {
    var userName = $(this).attr('id');
    $(this).parent('p').remove();
    $.post('/Group/rejectRequest', {
        userName: userName
    }, function (response) {
        loadRequests();
    });
});

////////////////////////////Edit Info///////////////////////////
/**
 * On click button 'editInfo' show form of update data-group
 */
$(document).on("click", "#editInfo", function () {
    $.post('/Group/updateGroupForm', {}, function (response) {
        $('.groupDetailDiv').html(response);
    });
});

/**
 * On click button 'editInfo' show form of update data-group
 */
$(document).on("click", "#cancelEdit", function () {
    $.post('/Group/groupDetails', {}, function (response) {
        $('.groupDetailDiv').html(response);
    });
});

setInterval(function () {
    loadChat();
}, 2000);

setInterval(function () {
    loadNames();
}, 10000);

$(document).ready(function () {
    setTimeout(function () {
        scrollDown();
    }, 100);
});