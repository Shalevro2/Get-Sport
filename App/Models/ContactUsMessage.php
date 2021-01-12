<?php

namespace App\Models;

use PDO;
use DateTime;
use \App\Token;
use \Core\View;


/**
 * Contact Us model of send message to admin
 *
 */
class ContactUsMessage extends \Core\Model
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
     * Save message on db
     *
     * @return bool Returns true if the query succeeded
     */
    public function save($userName, $messageType, $bodyMessage)
    {
        $sql = "INSERT INTO `contact_us`(`type`, `body_message`, `sender`) 
                VALUES (:messageType, :bodyMessage, :sender)";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':messageType', $messageType, PDO::PARAM_STR);
        $stmt->bindValue(':sender', $userName, PDO::PARAM_STR);
        $stmt->bindValue(':bodyMessage', $bodyMessage, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
    * function return all the Messages from contact us
    *
    * @return ContactUsMessage[] array of Messages object 
    */
    public static function getContactUsMessage()
    {
        $messagesArray = array();
        $sql = 'SELECT * FROM `contact_us` WHERE 1';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        while ($row = $stmt->fetchObject(get_called_class())) 
        {
			$messagesArray[] = $row;
		}

        return $messagesArray;
    }

    /**
    * Mark message as read
    *
    * @return void
    */
    public function markMessageAsReadById()
    {
        $sql = "UPDATE `contact_us` SET `is_read` = '1' WHERE `contact_us`.`id` = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
    * Mark message as unread
    *
    * @return void
    */
    public function markMessageAsUnreadById()
    {
        $sql = "UPDATE `contact_us` SET `is_read` = '0' WHERE `contact_us`.`id` = :id";

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
    * Find a ContactUsMessage model by ID
    *
    * @param string $id The Message ID
    *
    * @return mixed ContactUsMessage object if found, false otherwise
    */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM `contact_us` WHERE `id` = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    
    /**
     *delete record from db
     *@param int id of message
     *@return boolean  True if successful delete
     */
    public function deleteRecord()
    {
        $sql = "DELETE FROM `contact_us` WHERE id = :id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);


        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
}

?>