<?php
namespace App\Models;

use PDO;
use \Core\View;

/**
 * Sport category model
 *
 */
class SportCategory extends \Core\Model
{
    /**
     * function return categories from SportCategory table 
     * 
     */
    public static function allSportCategory()
    {

        $db = static::getDB();
        $sql = "SELECT * FROM sportcategory ";
        $stmt = $db->prepare($sql);
        $stmt->execute();
       
       return $stmt;
    }

    /**
     * Method return the name of the Sport Category that most common among users from the city with the most users
     * @return string Sport Category name
     */
    public static function mostCommonSportInTheMostCommonCity(){

        $sql = "SELECT sportcategory.SportCategoryName
                FROM sportcategory
                WHERE sportcategory.SportCategoryCode = 
                    (SELECT groupsport.SportCategoryCode
                     FROM groupsport
                     WHERE groupsport.cityCode = (SELECT users.cityCode
                                                  FROM users 
                                                  GROUP BY users.cityCode
                                                  ORDER BY COUNT(users.cityCode) DESC 
                                                  LIMIT 1)
                    GROUP BY groupsport.SportCategoryCode
                    ORDER BY COUNT(groupsport.SportCategoryCode) DESC 
                LIMIT 1)";
        
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->execute();
        
		return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}
?>