<?php

namespace App\Models;

use PDO;
use DateTime;
use \App\Token;
use \App\Mail;
use \Core\View;
use \App\Models\City;

/**
 * Group model
 *
 */
class GroupSport extends \Core\Model
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
     * Save the group model with the current property values
     *
     * @return boolean  True if the group was saved, false otherwise
     */
    public function save($userName)
    {
        $this->validate();

        if (empty($this->errors)) {
            
            $sql = 'INSERT INTO groupsport (GroupName, cityCode, SportCategoryCode, descrption, userNameManager, datePlay, timePlay)
                    VALUES (:GroupName, :cityCode, :SportCategoryCode, :descrption, :userNameManager, :datePlay, :timePlay)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':GroupName', $this->groupName, PDO::PARAM_STR);
            $stmt->bindValue(':cityCode', $this->cityCode, PDO::PARAM_INT);
            $stmt->bindValue(':SportCategoryCode', $this->SportCategoryCode, PDO::PARAM_INT);
            $stmt->bindValue(':descrption', $this->description, PDO::PARAM_STR);
            $stmt->bindValue(':userNameManager', $userName, PDO::PARAM_STR);
            $stmt->bindValue(':datePlay', $this->datePlay, PDO::PARAM_STR);
            $stmt->bindValue(':timePlay', $this->timePlay, PDO::PARAM_STR);
            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        // Group Name required
        if ($this->groupName == '') {
            $this->errors[] = 'Group name is required';
        }

        //Group Name exists
        if (static::groupNameExists($this->groupName)) {
            $this->errors[] = 'Group Name already taken';
        }

    }

    /**
     * See if a groupsport record already exists with the specified GroupName
     *
     * @param string $groupName group name to search for
     *
     * @return boolean  True if a record already exists with the specified groupName, false otherwise
     */
    public static function groupNameExists($groupName)
    {
        $groupsport = static::findByGroupName($groupName);

        if ($groupsport) {
                return true;
        }

        return false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $groupName group name to search for
     *
     * @return mixed GroupSport object if found, false otherwise
     */
    public static function findByGroupName($groupName)
    {
        $sql = 'SELECT * FROM groupsport WHERE GroupName = :GroupName';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }


    /**
     *delete record from db
     *@param string groupName 
     *@return boolean  True if successful delete
     */
    public function deleteRecord($groupName)
    {
        $sql = "DELETE FROM `groupsport` WHERE `GroupName` = :GroupName ";
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);
        
        return $stmt->execute();
    }

    
    /**
     *function get sportCategoryCode and cityCode and return groups
     *@param int sportCategoryCode code of sport category
     *@param int cityCode code of city 
     *@return array Groups array
     */
    public static function getGroupsBySCAndCity($sportCategoryCode, $cityCode)
    {
        $groupsArray = array();
        $sql = "SELECT * FROM `groupsport` WHERE `cityCode` = :cityCode AND `SportCategoryCode` =:SportCategoryCode";
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':cityCode', $cityCode, PDO::PARAM_STR);
        $stmt->bindValue(':SportCategoryCode', $sportCategoryCode, PDO::PARAM_STR);

        $stmt->execute();
        
        while ($row = $stmt->fetchObject(get_called_class())) 
        {
			$groupsArray[] = $row;
		}

        return $groupsArray;   
    }

    /**
    *function get user name and return groups that the user manage
    *@param string user name
    *@return array Groups array
    */
    public static function getGroupsByUserManager($userName)
    {
        $groupsArray = array();
        $sql = "SELECT * FROM `groupsport` WHERE `userNameManager` = :userNameManager";
        $db = static::getDB();
        $stmt = $db->prepare($sql);


        $stmt->bindValue(':userNameManager', $userName, PDO::PARAM_STR);

        $stmt->execute();
        
        while ($row = $stmt->fetchObject(get_called_class())) 
        {
			$groupsArray[] = $row;
		}

        return $groupsArray;
    }

    /**
     * Method return number of groups by category
     */
    public static function amountGroupByCategory($sportCategoryCode){

        $sql = "SELECT * FROM `groupsport` WHERE `SportCategoryCode` = :sportCategoryCode";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':sportCategoryCode', $sportCategoryCode, PDO::PARAM_INT);

        $stmt->execute();
        $count = $stmt->rowCount();
        
		return $count;
    }

    /**
     * Method return number of groups in month
     * Show only months are that in the db
     */
    public static function numOfGroupsInMonth()
    {

        $sql = "SELECT month(`datePlay`), count(*) FROM groupsport GROUP BY month(`datePlay`)";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();
        $monthAmountArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
		return $monthAmountArr;
    }

    /**
     * Method return number of groups
     */
    public static function countOfGroups(){

        $sql = "SELECT * FROM `groupsport` WHERE 1";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
        
		return $count;
    }

    /**  
     * function get user name and return groups that he not manage but member in the group
     *@param string user name
     *@return Groups[] Groups array
     */
    public static function getGroupsByMembership($userName)
    {
        $groupsArray = array();
        $sql = "SELECT * FROM groupsport 
                WHERE groupsport.userNameManager != :userName and groupsport.GroupName 
                IN (SELECT usersgroups.GroupName FROM usersgroups 
                    WHERE usersgroups.userName = :userName and is_accepted = 1)";
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':userName', $userName, PDO::PARAM_STR);

        $stmt->execute();
        
        while ($row = $stmt->fetchObject(get_called_class())) 
        {
			$groupsArray[] = $row;
		}

        return $groupsArray;
    }


    /**
    *function return data for calendar
    *@return array Groups array
    */
    public static function getDataForCalendar()
    {
        $groupsArray = array();
        $sql = "SELECT `GroupName`,`datePlay`,`timePlay`,`cityCode`,`userNameManager` 
                FROM `groupsport` WHERE 1";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        $i = 0;
        foreach($result as $row){
            $end = date("d-m-Y H:i:s", strtotime($row["datePlay"]." ".$row["timePlay"])+7200);
            
            $data[] = array(
                'id'   => $i,
                'title'   => $row["GroupName"]." (".City::getCityByID($row['cityCode']).") ",
                'start'   => $row["datePlay"]." ".$row["timePlay"],
                'end'   => $end, 
                'color'  => 'green',   
                'textColor' => 'white'
            );
            $i++;
        }
        return $data;
    }

    /**
     * Update the group
     *
     * @param array $data Data from the update group form
     *
     * @return boolean  True if the data was updated, false otherwise
     */
    public function updateGroup($data)
    {
        $this->descrption = $data['description'];
        $this->cityCode = $data['cityCode'];
        $this->datePlay = $data['datePlay'];
        $this->timePlay = $data['timePlay'];
               
        $sql = 'UPDATE `groupsport` 
                SET `cityCode`=:cityCode,`descrption`=:descrption ,`datePlay`=:datePlay ,`timePlay`=:timePlay 
                WHERE GroupName=:GroupName';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        
        $stmt->bindValue(':GroupName', $this->GroupName, PDO::PARAM_STR);
        $stmt->bindValue(':cityCode', $this->cityCode, PDO::PARAM_INT);
        $stmt->bindValue(':descrption', $this->descrption, PDO::PARAM_STR);
        $stmt->bindValue(':datePlay', $this->datePlay, PDO::PARAM_STR);
        $stmt->bindValue(':timePlay', $this->timePlay, PDO::PARAM_STR);

        return $stmt->execute();
    }
}

?>