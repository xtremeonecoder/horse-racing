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

/*
 * Class RaceModel
 */
class RaceModel
{
    /**
     * @var RaceModel Hold the class instance.
     */
    private static $_instance = null;
    
    /**
     * DB table name
     */
    private $_tableName = 'race';

    /**
     * race not finished
     */
    private $_notFinished = 0;

    /**
     * race finished
     */
    private $_finished = 1;

    /**
     * @var PDO
     */
    private $_dbConnection;

    /**
     * RaceModel constructor
     */
    public function __construct()
    {
        $database = DatabaseLibrary::getInstance();
        $this->_dbConnection = $database->getConnection();
    }
    
    /**
     * get db instance
     * @return RaceModel
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new RaceModel();
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
    
    /*
     * race finished
     * @return boolean 
     */
    public function raceFinished()
    {
        return $this->_finished;
    }
    
    /*
     * race not finished
     * @return boolean 
     */
    public function raceNotFinished()
    {
        return $this->_notFinished;
    }

    /**
     * get active races
     * @return array
     */
    public function getActiveRaces()
    {
        // prepare retrieval sql
        $sql = "
            SELECT
                `race_id`, `race_progress_time`, `race_finish_time`, `race_finished` 
            FROM 
                `{$this->getTableName()}`
            WHERE 
                race_finished = {$this->raceNotFinished()}
        ";
        $stmt = $this->_dbConnection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * insert race
     * @param $finishTime
     * @return bool|string
     */
    public function insert($finishTime)
    {
        // prepare insertion sql
        $sql = "
            INSERT INTO 
                `{$this->getTableName()}` (`race_finish_time`)
            VALUES 
                (:race_finish_time)
        ";
        $params = array('race_finish_time' => $finishTime);
        $stmt = $this->_dbConnection->prepare($sql);
        $result = $stmt->execute($params);
        if ($result) {
            return $this->_dbConnection->lastInsertId();
        }
        return false;
    }

    /**
     * update race
     * @param $params
     * @return bool
     */
    public function update($params)
    {
        // prepare update sql
        $sql = "
            UPDATE
                `{$this->getTableName()}`
            SET
                race_progress_time=:race_progress_time, race_finished=:race_finished 
            WHERE
                race_id=:race_id
        ";
        $stmt= $this->_dbConnection->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * get latest races
     * @param $count
     * @return array
     */
    public function getLatest($count)
    {
        // prepare retrieval sql
        $sql = "
            SELECT
                `race_id`, `race_progress_time`, `race_finish_time`, `race_finished` 
            FROM
                `{$this->getTableName()}`
            WHERE
                race_finished = {$this->raceFinished()}
            ORDER BY
                `race_id` DESC
            LIMIT
                {$count}
        ";
        $stmt = $this->_dbConnection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}