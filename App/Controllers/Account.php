<?php

namespace App\Controllers;

use \App\Models\User;

/**
 * Account controller
 *
 */
class Account extends \Core\Controller
{

    /**
     * Validate if email is available (AJAX) for a new signup or an existing user.
     * The ID of an existing user can be passed in the querystring to ignore when
     * checking if an email already exists or not.
     *
     * @return void
     */
    public function validateEmailAction()
    {
        $is_valid = ! User::emailExists($_GET['email'], $_GET['ignore_id'] ?? null);
        
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    /**
     * Validate if user name is available (AJAX) for a new signup or an existing user.
     *
     * @return void
     */
    public function validateUserNameAction()
    {
        $is_valid = ! User::userNameExists($_GET['userName']);
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }
}
