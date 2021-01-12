<?php

namespace App\Models;

use PDO;
use DateTime;
use \App\Token;
use \App\Mail;
use \Core\View;
use \App\Models\City;
use \App\Models\User;

/**
 * Group model
 *
 */
class UsersGroups extends \Core\Model
{
    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the group model
     * @param string userName the user name to save
     * @param string groupName the group name to save the user
     * @return boolean  True if the group was saved, false otherwise
     */
    public function save($userName, $groupName)
    {            
        $sql = "INSERT INTO `usersgroups`(`GroupName`, `userName`) VALUES (:groupName, :userName)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':groupName', $groupName, PDO::PARAM_STR);
        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);
            
        return $stmt->execute();

    }

    /**
     * Save the group model
     * A new record when admin invite user
     * 
     * @param string userName the user name to save
     * @param string groupName the group name to save the user
     * 
     * @return boolean  True if the group was saved, false otherwise
     */
    public function groupAdminInvite($userName, $groupName)
    {

        $sql = "INSERT INTO `usersgroups`(`GroupName`, `userName`,`is_accepted`, `gAdmin_invitation`)
                VALUES (:groupName, :userName, 0, 1)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':groupName', $groupName, PDO::PARAM_STR);
        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);
            
        return $stmt->execute();

    }


    /**
     * Method return number of members in group
     */
    public static function countOfMembers($groupName){

        $sql = "SELECT * FROM `usersgroups` WHERE `GroupName`=:GroupName and is_accepted = 1";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);

        $stmt->execute();
        $count = $stmt->rowCount();
        
		return $count;
    }
    
    /**
     *function check if user already in group
     *@param string groupName 
     *@param string userName 
     *@return int 1 if exists in group else 0
     */
    public static function isUserInGroup($groupName, $userName)
    {
        $sql = "SELECT * FROM usersgroups WHERE GroupName=:GroupName AND userName=:userName";
        $db = static::getDB();
        $stmt = $db->prepare($sql);


        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);
        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);
        
        $stmt->execute();
        
        $count = $stmt->rowCount();
        return $count;
    
    }

    /**
     * function get group name and return all userNames that in group
     * @param string group name
     * @return string[] user name array
     */
	public static function groupUserNames($groupName){
        $groupsArray = array();
        
        $sql = "SELECT `userName` FROM `usersgroups` WHERE `GroupName` = :GroupName and is_accepted = 1";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);

        $stmt->execute();

		while ($row = $stmt->fetchObject()) {
			$groupsArray[] = $row;
        }
                
		return $groupsArray;
    }
    
    /**
     *function check if user requerst is accepted
     *@param string groupName 
     *@param string userName 
     *@return int 1 if is accepted else 0
     */
    public static function isAccepted($groupName, $userName)
    {
        $sql = "SELECT * FROM usersgroups WHERE GroupName=:GroupName AND userName=:userName AND is_accepted = 1";
        $db = static::getDB();
        $stmt = $db->prepare($sql);


        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);
        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);
        
        $stmt->execute();
        
        $count = $stmt->rowCount();
        return $count;
    }

    /**
     *accept the request
     *@param string groupName 
     *@param string userName 
     *@return boolean  True if successful update
     */
    public function acceptRequest($groupName, $userName)
    {
        $sql = "UPDATE usersgroups SET is_accepted = 1, gAdmin_invitation = 0
                 WHERE GroupName=:GroupName AND userName=:userName";
        $db = static::getDB();
        $stmt = $db->prepare($sql);


        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);
        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     *delete record from db
     *@param string groupName 
     *@param string userName 
     *@return boolean  True if successful delete
     */
    public function deleteRecord($groupName, $userName)
    {
        $sql = "DELETE FROM `usersgroups` WHERE `GroupName` = :GroupName AND `userName`= :userName ";
        $db = static::getDB();
        $stmt = $db->prepare($sql);


        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);
        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    /**
     * function get group name and return all userNames that request to join
     * @param string group name
     * @return string[] userName array
     */
	public static function groupUserNamesRequest($groupName){
        $groupsArray = array();
        
        $sql = "SELECT `userName` FROM `usersgroups` 
                WHERE `GroupName` = :GroupName and is_accepted = 0 and gAdmin_invitation = 0";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);

        $stmt->execute();

		while ($row = $stmt->fetchObject()) {
			$groupsArray[] = $row;
        }
                
		return $groupsArray;
    }

    /**
     * function get group name and number of userNames that request to join
     * @param string group name
     * @return int amount of user that request to join
     */
	public static function countUserNamesRequest($groupName){
        $groupsArray = array();
        
        $sql = "SELECT `userName` FROM `usersgroups` 
                WHERE `GroupName` = :GroupName and is_accepted = 0 and gAdmin_invitation = 0";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);

        $stmt->execute();
                
		return $stmt->rowCount();
    }

    /**
     * function get user name and return all admin invitations
     * @param string user name
     * @return string[] invitations array
     */
	public static function adminInvitation($userName){
        $invitationsArray = array();
        
        $sql = "SELECT * FROM `usersgroups` 
                WHERE `userName` = :userName and `gAdmin_invitation` = 1 and is_accepted = 0";

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);

        $stmt->execute();

		while ($row = $stmt->fetchObject()) {
			$invitationsArray[] = $row;
        }
                
		return $invitationsArray;
    }


    /**
     * Method return userName of users that not in group
     * @param string group name
     * @return string[] array of userName
     */
    public static function usersNotInGroup($groupName){

        $sql = "SELECT users.userName 
                FROM users 
                WHERE users.userName != 'admin' and users.userName NOT IN 
                (SELECT usersgroups.userName FROM usersgroups WHERE usersgroups.GroupName = :GroupName )";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);

        $stmt->execute();
        
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Method return the group name with the most users
     * @return string group name
     */
    public static function groupWithMostUsers(){

        $sql = "SELECT `GroupName`,COUNT(`GroupName`) mycount 
                FROM usersgroups 
                WHERE `is_accepted` = 1 
                GROUP BY `GroupName` 
                ORDER BY mycount DESC 
                LIMIT 1";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();
        
		return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

?>