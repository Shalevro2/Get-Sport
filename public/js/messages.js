'use strict';

////////////////////////////Remove message///////////////////////////
/**
 * On click button 'removeMessage' remove meesage from table
 */
$(document).on("click", ".removeMessage", function () {
    var messageId = $(this).attr('id');
    $(this).closest("tr").remove();
    $.post('/admin/removeMessage', {
        messageId: messageId
    }, function (response) {});
});