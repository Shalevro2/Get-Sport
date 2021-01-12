<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;
use \App\Models\ContactUsMessage;

/**
 * Contact us controller
 */
class ContactUs extends Authenticated
{

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
    * Show Contact-us page
    */
    public function showAction()
    {        
        View::renderTemplate('ContactUs/show.html', [
            'user' => $this->user]);
    }

    /**
    * Send message from contact us-> from user to admin
    */
    public function sendAction()
    {
        $conUs = new ContactUsMessage();
        if ($conUs->save($this->user->userName, $_POST['selectTypeMessage'], $_POST['bodyMessage'])) {
            
            Flash::addMessage('Message was sent successfully', Flash::SUCCESS);

            View::renderTemplate('ContactUs/show.html', ['user' => $this->user]);
        }
        else{
            Flash::addMessage('There was a problem sending the message', Flash::WARNING);

            View::renderTemplate('ContactUs/show.html', ['user' => $this->user]);
        }
    }    

}