<?php
/**
 * Horse Race Simulator
 *
 * @category   Application_Core
 * @package    horse-race-simulator
 * @author     Suman Barua
 * @developer  Suman Barua <sumanbarua576@gmail.com>
 */

// Direct access should be denied
defined('BASE_URL') || exit('Access not allowed!');

/**
 * Class HorseModel
 */
class HorseModel
{
    /**
     * @var HorseModel Hold the class instance.
     */
    private static $_instance = null;
    
    /**
     * DB table name
     */
    private $_tableName = 'horse';

    /**
     * @var PDO
     */
    private $_dbConnection;

    /**
     * HorseModel constructor.
     */
    public function __construct()
    {
        $database = DatabaseLibrary::getInstance();
        $this->_dbConnection = $database->getConnection();
    }
    
    /**
     * get db instance
     * @return HorseModel
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new HorseModel();
        }

        return self::$_instance;
    }
    
    /*
     * get table name
     * @return string 
     */
    public function getTableName()
    {
        return $this->_tableName;
    }

    /**
     * insert horse to DB
     * @param $raceID
     * @param HorseLibrary $horse
     * @return bool|string last insert ID or false
     */
    public function insert($raceID, $horse)
    {
        // preparing parameters
        $params = array(
            'race_id' => $raceID,
            'horse_speed' => $horse->getSpeed(),
            'horse_strength' => $horse->getStrength(),
            'horse_endurance' => $horse->getEndurance(),
            'elapsed_time' => $horse->getElapsedTime(),
        );
        
        // prepare insertion sql
        $sql = "
            INSERT INTO `{$this->getTableName()}`
                (`race_id`, `horse_speed`, `horse_strength`, `horse_endurance`, `elapsed_time`)
            VALUES
                (:race_id, :horse_speed, :horse_strength, :horse_endurance, :elapsed_time);
        ";
        $stmt = $this->_dbConnection->prepare($sql);
        $result = $stmt->execute($params);
        if ($result) {
            $horseID = (int) $this->_dbConnection->lastInsertId();
            $horse->setId($horseID);
            return $horseID;
        }
        return false;
    }

    /**
     * insert multiple horses to DB
     * @param $raceID
     * @param []HorseLibrary $horses
     * @return array horse IDs
     */
    public function insertMultiple($raceID, $horses)
    {
        $horseIDs = array();
        foreach ($horses as $horse) {
            $horseID = $this->insert($raceID, $horse);
            if ($horseID === false) {
                error_log("ERROR - insert multiple horses failed! Race ID: {$raceID}");
            } else {
                $horseIDs[] = (int) $horseID;
            }
        }
        return $horseIDs;
    }

    /**
     * get horses by race ID
     * @param $raceID
     * @return array
     */
    public function getHorsesByRaceID($raceID)
    {
        // prepare retrieve sql
        $params = array('race_id' => $raceID);
        $sql = "
            SELECT
                `horse_id`, `horse_speed`, `horse_strength`, `horse_endurance`
            FROM 
                `{$this->getTableName()}` 
            WHERE 
                `race_id` = :race_id 
            ORDER BY 
                `elapsed_time`
        ";
        $stmt = $this->_dbConnection->prepare($sql);
        $stmt->execute($params);
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get top horses by race ID
     * @param $raceID
     * @param $count
     * @return array
     */
    public function getTopHorsesByRaceID($raceID, $count)
    {
        // prepare retrieve sql
        $params = array('race_id' => $raceID);
        $sql = "
            SELECT 
                `horse_id`, `horse_speed`, `horse_strength`, `horse_endurance` 
            FROM 
                `{$this->getTableName()}` 
            WHERE 
                `race_id` = :race_id 
            ORDER BY 
                `elapsed_time` 
            LIMIT {$count}
        ";
        $stmt = $this->_dbConnection->prepare($sql);
        $stmt->execute($params);
        return  $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get top horse
     * @return array
     */
    public function getTopHorse()
    {
        $race = RaceModel::getInstance();
        $sql = "
            SELECT 
                h.`horse_id`, h.`horse_speed`, h.`horse_strength`, h.`horse_endurance`, h.`elapsed_time` 
            FROM 
                `{$this->getTableName()}` h
            INNER JOIN 
                `{$race->getTableName()}` r ON h.`race_id` = r.`race_id`
            WHERE 
                r.`race_finished` = {$race->raceFinished()} 
            ORDER BY 
                h.`elapsed_time` 
            LIMIT 1
        ";
        $stmt = $this->_dbConnection->prepare($sql);
        $stmt->execute();
        return  $stmt->fetch(PDO::FETCH_ASSOC);
    }
}