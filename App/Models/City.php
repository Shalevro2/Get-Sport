<?php
namespace App\Models;

use PDO;
use \Core\View;

/**
 * City Model
 */
class City extends \Core\Model
{
    /**
     * function return cities from city table 
     * 
     */
    public static function allCity()
    {
        $db = static::getDB();
        $sql = "SELECT * FROM city ORDER BY cityName";
        $stmt = $db->prepare($sql);
        $stmt->execute();
       
       return $stmt;
    }

    /**
     * function get city code and return name of city
     * @param int city code
     * @return string city name 
     */
    public static function getCityByID(int $id)
    {    
        $sql = "SELECT cityName FROM city WHERE cityCode = :id";
        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['cityName'];
	}

}
?>