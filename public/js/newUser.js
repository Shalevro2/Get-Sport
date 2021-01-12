'use strict';
$(document).ready(function () {
    inputDateOfBirth.max = new Date().toISOString().split("T")[0];
    /**
     * Validate the form
     */
    $('#formSignup').validate({
        rules: {
            name: 'required',
            email: {
                required: true,
                email: true,
                remote: '/account/validate-email'
            },
            userName: {
                required: true,
                remote: '/account/validateUserNameAction'
            },
            password: {
                required: true,
                minlength: 6,
                validPassword: true
            }
        },
        messages: {
            email: {
                remote: 'email already taken'
            },
            userName: {
                remote: 'User Name already taken'
            }
        }
    });


    /**
     * Show password toggle button
     */
    $('#inputPassword').hideShowPassword({
        show: false,
        innerToggle: 'focus'
    });
});