<?php

namespace App\Models;

use PDO;
use DateTime;
use \App\Token;
use \Core\View;


/**
 * Messages model of chat messages
 *
 */
class Messages extends \Core\Model
{

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
     * function return all the Messages of group
     *
     * @return Messages[] array of Messages object 
     */
    public static function getMessagesByGroupName($groupName)
    {
        $messagesArray = array();
        $sql = 'SELECT * FROM `messages` WHERE `GroupName` = :GroupName';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        while ($row = $stmt->fetchObject(get_called_class())) 
        {
			$messagesArray[] = $row;
		}

        return $messagesArray;
    }

    /**
     * Save message on db
     *
     * @return bool Returns true if the query succeeded
     */
    public function save($userName, $groupName, $messageBody)
    {
        $sql = "INSERT INTO `messages`(`sender`, `GroupName`, `messageBody`) 
                VALUES (:sender, :GroupName, :messageBody)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':GroupName', $groupName, PDO::PARAM_STR);
        $stmt->bindValue(':sender', $userName, PDO::PARAM_STR);
        $stmt->bindValue(':messageBody', $messageBody, PDO::PARAM_STR);

        return $stmt->execute();

    }

    /**
     * Method return number of Messages
     * @return int number of messages
     */
    public static function countOfMessages(){

        $sql = "SELECT * FROM `messages` WHERE 1";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();
        $count = $stmt->rowCount();
        
		return $count;
    }

    /**
     * Method return the group name with the most Messages
     * @return string group name
     */
    public static function groupWithMostMessages(){

        $sql = "SELECT `GroupName`,COUNT(`GroupName`) mycount 
                FROM messages 
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