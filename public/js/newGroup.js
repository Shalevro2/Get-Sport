'use strict';
$(document).ready(function () {
    inputDate.min = new Date().toISOString().split("T")[0];
    /**
     * Validate the form
     */
    $('#formCreateGroup').validate({
        rules: {
            groupName: 'required',
            groupName: {
                required: true,
                remote: '/Group/validate-groupName'
            },
        },
        messages: {
            groupName: {
                remote: 'Group Name already taken'
            }
        }
    });
});