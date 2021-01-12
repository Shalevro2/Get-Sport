<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

use \App\Models\User;


/**
 * Users controller
 */

 class Users extends Authenticated
 {
    /**
     * Before filter - called before each action method
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
     * show the Users
     */
     public function showUsersAction()
     {
        $users = User::getAllUsers();
        
        View::renderTemplate('Users/showUsers.html', [
            'user' => $this->user,
            'allUsers' => $users]);
     }

 }