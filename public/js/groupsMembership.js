'use strict';
//////////////////////////////Invitations///////////////////////////////

/**
 * Accepet the invitation and join the group
 */
$('.btnAccept').click(function (e) {
    var GroupName = $(this).attr('id');
    $(this).parent('p').remove();
    $.post('/Group/acceptInvitation', {
        GroupName: GroupName
    }, function (response) {
        loadGroups();
        setTimeout(function () {
            divSportBGFunc();
        }, 1000);
    });
});


/**
 * load Groups into div
 */
function loadGroups() {
    $('#mainGroups').load(' #mainGroups');
}

/**
 * Reject the invitation
 */
$('.btnReject').click(function (e) {
    var GroupName = $(this).attr('id');
    $(this).parent('p').remove();
    $.post('/Group/rejectInvitation', {
        GroupName: GroupName
    }, function (response) {});
});