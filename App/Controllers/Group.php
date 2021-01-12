<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\GroupSport;
use \App\Models\UsersGroups;
use \App\Models\City;
use \App\Models\User;
use \App\Models\Messages;




/**
 * Groups controller
 */
class Group extends Authenticated
{
    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    /**
    * Show the groups
    */
    public function showGroupsAction()
    {
        $groups = $this->showGroupBySCAndCAction();

        View::renderTemplate('Group/showGroups.html', [
            'user' => $this->user, 
            'Groups' => $groups,
            'post' => $_POST]);
    }

    
    /**
    * Show the groups that the user is a member
    */
    public function groupsMembershipAction()
    {
        $groups = GroupSport::getGroupsByMembership($this->user->userName);
        $groups = $this->addAmountOfMembers($groups);
        $invitations = UsersGroups::adminInvitation($this->user->userName);

        View::renderTemplate('Group/groupsMembership.html', [
            'user' => $this->user, 
            'Groups' => $groups,
            'post' => $_POST,
            'invitations' => $invitations ]);
    }


    /**
    * Show the groups the user manage
    */
    public function groupsManagementAction()
    {
        $groups = GroupSport::getGroupsByUserManager($this->user->userName);
        $groups = $this->addAmountOfMembers($groups);
        $groups = $this->addAmountOfNotification($groups);
        
        View::renderTemplate('Group/groupsManagement.html', [
            'user' => $this->user, 
            'Groups' => $groups,
            'post' => $_POST]);
    }

    /**
     * Show group page
     */
    public function groupPageAction()
    {
       if(!isset($_SESSION['groupNameToDisplay'])){
           $this->redirect('/Group/showGroups');
       }
       else{
           $group = GroupSport::findByGroupName($_SESSION['groupNameToDisplay']);
           $users = UsersGroups::groupUserNames($_SESSION['groupNameToDisplay']);
           $requests = UsersGroups::groupUserNamesRequest($_SESSION['groupNameToDisplay']);
           $usersUserName = UsersGroups::usersNotInGroup($_SESSION['groupNameToDisplay']);
           $cityName = City::getCityByID($group->cityCode);
           View::renderTemplate('Group/groupPage.html', [
               'user' => $this->user, 
               'group' => $group,
               'city' => $cityName,
               'users' => $users,
               'requests' => $requests,
               'usersUserName' => $usersUserName]);
       }
    }


    /**
     * Show the form for new Group
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Group/new.html', [
            'user' => $this->user]);
    }

    /**
     * Join group
     *
     * @return void
     */
    public function joinAction()
    {
        $usersGroups = new UsersGroups();
        $_SESSION['groupNameToDisplay'] =  $_POST['btnJoin'];

        if(UsersGroups::isUserInGroup($_POST['btnJoin'], $this->user->userName) == 0)
        {//if user not in group send a request
            $usersGroups->save($this->user->userName, $_POST['btnJoin']);
            Flash::addMessage('Your request has been sent', Flash::SUCCESS);
            $this->redirect('/Group/showGroups');
        }
        else{
            if(UsersGroups::isAccepted($_POST['btnJoin'], $this->user->userName)){//if user in group go to groupPage
                $this->redirect('/Group/groupPage');
            }
            else{//if user already send a request
                Flash::addMessage('Your request is awaiting approval from the group administrator', Flash::WARNING);
                $this->redirect('/Group/showGroups');
            }
        }
    }

    /**
     * Add a new Group
     *
     * @return void
     */
    public function createAction()
    {
        $group = new GroupSport($_POST);
        $usersGroups = new UsersGroups();

        if ($group->save($this->user->userName)) {
            $usersGroups->save($this->user->userName, $group->groupName);
            $usersGroups->acceptRequest($group->groupName, $this->user->userName);
            Flash::addMessage('Group was successfuly created', Flash::SUCCESS);

            $_SESSION['groupNameToDisplay'] = $group->groupName;
            $this->redirect('/Group/groupPage');

        } else {

            View::renderTemplate('Group/new.html', [
                'user' => $user
            ]);
        }
    }


    /**
     * Show groups by sport category anc city
     * 
     * @return Group[] group array
     */
    public function showGroupBySCAndCAction()
    {
        $groups = GroupSport::getGroupsBySCAndCity($_POST['SportCategoryCode'] ?? 1, $_POST['cityCode'] ?? $this->user->cityCode);
               
        return $this->addAmountOfMembers($groups);
    }

    /**
     * function add attribute of members to group
     * @param Group[] group array
     * @return Group[] group array
     */
    private function addAmountOfMembers($groups)
    {
        for($i = 0;$i < count($groups); $i++)
            $groups[$i]->Members = UsersGroups::countOfMembers($groups[$i]->GroupName);

        return $groups;
    }

    /**
     * function add attribute of notification  to group
     * @param Group[] group array
     * @return Group[] group array
     */
    private function addAmountOfNotification($groups)
    {
        for($i = 0;$i < count($groups); $i++)
            $groups[$i]->Notification = UsersGroups::countUserNamesRequest($groups[$i]->GroupName);

        return $groups;
    }

    /**
     * Validate group name 
     * check if the group name already taken
     */
    public function validateGroupNameAction()
    {
        $is_valid = ! GroupSport::groupNameExists($_GET['groupName'], $_GET['ignore_id'] ?? null);
        header('Content-Type: application/json');
        echo json_encode($is_valid);
    }

    /**
     * Get the chat messages
     */
    public function getChatAction()
    {
        $messageArr = Messages::getMessagesByGroupName($_SESSION['groupNameToDisplay']);
                
        View::renderTemplate('Group/chat.html', [
            'user' => $this->user,
            'messageArr' => $messageArr]);
              
    }

    /**
     * Save message on db.
     * action when send message in chat
     */
    public function sendMessageAction()
    {
        $newMessage = new Messages();
        $newMessage->save($this->user->userName, $_SESSION['groupNameToDisplay'], $_POST['message']);
    }

    /**
     * Accepet request to join group
     * the request come from the user to group manager.
     */
    public function acceptRequestAction()
    {
        $usersGroups = new UsersGroups();
        $usersGroups->acceptRequest($_SESSION['groupNameToDisplay'] ,$_POST['userName']);
    }

    /**
     * Accepet invitation to join group
     * the invite come from the group manager to user.
     */
    public function acceptInvitationAction()
    {
        $usersGroups = new UsersGroups();
        $usersGroups->acceptRequest($_POST['GroupName'], $this->user->userName);
    }

    /**
     * Get the userNames of users that members in group
     */
    public function getUsersInGroupAction()
    {
        $users = UsersGroups::groupUserNames($_SESSION['groupNameToDisplay']);
        $group = GroupSport::findByGroupName($_SESSION['groupNameToDisplay']);

        View::renderTemplate('Group/userInGroup.html', [
            'user' => $this->user,
            'users' => $users,
            'group' => $group]);
    }

    /**
     * Reject the request.
     * the request come from user to group manager.
     */
    public function rejectRequestAction()
    {
        $usersGroups = new UsersGroups();
        $usersGroups->deleteRecord($_SESSION['groupNameToDisplay'] ,$_POST['userName']);
    }

    /**
     * Reject the Invitation.
     * the invite come from group manager to user.
     */
    public function rejectInvitationAction()
    {
        $usersGroups = new UsersGroups();
        $usersGroups->deleteRecord($_POST['GroupName'], $this->user->userName);
    }

    /**
     * Get the request to join the group.
     */
    public function getRequestsAction()
    {
        $requests = UsersGroups::groupUserNamesRequest($_SESSION['groupNameToDisplay']);

        View::renderTemplate('Group/requests.html', [
            'user' => $this->user,
            'requests' => $requests]);
    }

    /**
     * Invite users to join group.
     * the invitation come from group manager.
     */
    public function inviteUsersAction()
    {
        if(isset($_POST['userNames'])){
            $usersGroups = new UsersGroups();
            $usersArr = $_POST['userNames'];

            for($i = 0 ; $i < count($usersArr) ; $i++)
                $usersGroups->groupAdminInvite($usersArr[$i], $_SESSION['groupNameToDisplay']);
                
            $usersUserName = UsersGroups::usersNotInGroup($_SESSION['groupNameToDisplay']);

            View::renderTemplate('Group/addUsers.html', [
                'user' => $this->user,
                'usersUserName' => $usersUserName]);
        }
    }

    /**
     * Exit from group action.
     */
    public function exitGroupAction()
    {
        $usersGroups = new UsersGroups();
        $usersGroups->deleteRecord($_POST['groupName'] , $this->user->userName);
        Flash::addMessage('You left the group', Flash::WARNING);
        echo '/Group/showGroups';        
    }

    /**
     * Delete group action.
     */
    public function deleteGroupAction()
    {
        $groupSport = new GroupSport();
        $groupSport->deleteRecord($_POST['groupName']);
        Flash::addMessage('You deleted the group', Flash::WARNING);
        echo '/Group/showGroups';
    }

    /**
     * Render update group form
     */
    public function updateGroupFormAction()
    {
        $group = GroupSport::findByGroupName($_SESSION['groupNameToDisplay']);
        
        View::renderTemplate('Group/updateGroup.html', [
            'user' => $this->user,
            'group' => $group]);
              
    }

    /**
     * Render group details
     */
    public function groupDetailsAction()
    {
        $group = GroupSport::findByGroupName($_SESSION['groupNameToDisplay']);
        $cityName = City::getCityByID($group->cityCode);

        View::renderTemplate('Group/groupDetails.html', [
            'user' => $this->user,
            'group' => $group,
            'city' => $cityName]);
              
    }

    /**
     * Update the group details
     */
    public function updateAction()
    {
        $group = GroupSport::findByGroupName($_SESSION['groupNameToDisplay']);
        $group->updateGroup($_POST);

        $this->redirect('/Group/groupPage');             
    }

 }