<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;

use \App\Models\User;
use \App\Models\ContactUsMessage;
use \App\Models\GroupSport;
use \App\Models\UsersGroups;
use \App\Models\Messages;
use \App\Models\SportCategory;


/**
 * Admin controller
 */
 class Admin extends Authenticated
{

    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
    * show the panel admin
    */
    public function panelAction()
    {
        //Data for the html page
        $usersAgeAvg = User::usersAgeAvg();
        $groupWithMostUsers = UsersGroups::groupWithMostUsers();
        $groupWithMostMessages = Messages::groupWithMostMessages();
        $numOfUsers = User::countOfUsers();  
        $numOfGroups = GroupSport::countOfGroups();
        $numOfMessages = Messages::countOfMessages(); 
        $mostCommonSportInTheMostCommonCity = SportCategory::mostCommonSportInTheMostCommonCity();

        View::renderTemplate('Admin/panel.html', [
            'user' => $this->user,
            'numOfUsers' => $numOfUsers,
            'numOfGroups' => $numOfGroups,
            'numOfMessages' => $numOfMessages,
            'usersAgeAvg' => $usersAgeAvg,
            'groupWithMostUsers' => $groupWithMostUsers,
            'groupWithMostMessages' => $groupWithMostMessages,
            'mostCommonSportInTheMostCommonCity' => $mostCommonSportInTheMostCommonCity]);
    }

    /**
    * Manage the users
    * show users.html page
    */
    public function manageUsersAction()
    {
       $users = User::getAllUsers();

       View::renderTemplate('Admin/Users.html', [
           'user' => $this->user,
           'allUsers' => $users]);
    }

    /**
     * Block/Unblock user action
     * If button 'block' was clicked, block the user
     * If button 'unblock' was clicked, Active the user
     */
    public function blockUserAction()
    {
       if (isset($_POST['block'])) {
            //The value of $_POST['block'] is the user name to block or unblock
            $userToBlock = User::findByUserName($_POST['block']);
            //Check the user status (is_active)
            if ($userToBlock->is_active == 1) {
                $userToBlock->blockUserByIsActive();
            }else{
                $userToBlock->activeUserByIsActive();
            }

            $users = User::getAllUsers();
            View::renderTemplate('Admin/Users.html', [
                'user' => $this->user,
                'allUsers' => $users]);
        }
    }

    
    /**
     * Render the Messages.html template.
     * Contact us messages of admin.
     */
    public function messagesAction()
    {
       $messages = ContactUsMessage::getContactUsMessage();

       View::renderTemplate('Admin/Messages.html', [
           'user' => $this->user,
           'messages' => $messages]);
    }

    /**
     * Render the GamesSchedule.html template.
     * Games Schedule
     */
    public function gamesScheduleAction()
    {
       View::renderTemplate('Admin/GamesSchedule.html', [
           'user' => $this->user]);
    }

    /**
    * Mark as read message action.
    * Mard message as read in contact us admin.
    */
    public function markAsReadAction()
    {
        //If "markAsRead" button was clicked, update the column in the ContactUsMessage Table.
        if (isset($_POST['markAsReadBtn'])) {
            $messageToMark = ContactUsMessage::findByID($_POST['markAsReadBtn']);
            if($messageToMark){
                $messageToMark->markMessageAsReadById();
            }
            $messages = ContactUsMessage::getContactUsMessage();

            View::renderTemplate('Admin/Messages.html', [
                'user' => $this->user,
                'messages' => $messages]);
            
        }elseif (isset($_POST['markAsUnReadBtn'])) 
        {//If "markAsUnReadBtn" button was clicked, update the column in the ContactUsMessage Table.
            $messageToMark = ContactUsMessage::findByID($_POST['markAsUnReadBtn']);

            $messageToMark->markMessageAsUnreadById();
            $messages = ContactUsMessage::getContactUsMessage();

            View::renderTemplate('Admin/Messages.html', [
                'user' => $this->user,
                'messages' => $messages]);            
        }
    }
    
    /**
     * Remove message action.
     * delete a record from contact_us table.
     */
    public function removeMessageAction()
    {
        $messageToDelete = ContactUsMessage::findByID($_POST['messageId']);

        $messageToDelete->deleteRecord();    
    }

    /**
     * Get amount of groups in each sport category.
     * 1- Football
     * 2- basketball
     * 3- Tennis
     * 4- Running
     * 5- Gym
     */
    public function getAmountGroupsAction()
    {
        $arrCategoryGroupAmount = array(
            "Football" => GroupSport::amountGroupByCategory(1),
            "BasketBall" => GroupSport::amountGroupByCategory(2),
            "Tennis" => GroupSport::amountGroupByCategory(3),
            "Running" => GroupSport::amountGroupByCategory(4),
            "Gym" => GroupSport::amountGroupByCategory(5)
        );
        echo json_encode($arrCategoryGroupAmount);
    }
    
    /**
     * Get number of groups in each month.
     * The method return only months in which games take place.
     */
    public function getNumOfGroupsInMonthAction()
    {
        $groupMonth = GroupSport::numOfGroupsInMonth(); 
        echo json_encode($groupMonth);
    }

    /**
     * Get number of users in each city.
     * The method return only cities that has users.
     */
    public function getNumOfUsersInCityAction()
    {
        $userCity = User::numOfUsersInCity(); 
        echo json_encode($userCity);
    }

    /**
     * Show calendar action.
     * Get the data from db to calendar.
     */
    public function showCalendarAction()
    {
        $dataCalendar = GroupSport::getDataForCalendar();
        echo json_encode($dataCalendar);
    }

}