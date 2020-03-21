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
 * Class DatabaseLibrary
 */
class DatabaseLibrary
{
    //
    /**
     * @var DatabaseLibrary Hold the class instance.
     */
    private static $_instance = null;
    /**
     * @var PDO
     */
    private $_connection;

    /**
     * @var string db host
     */
    private $_host = 'localhost';
    /**
     * @var string db user
     */
    private $_user = 'root';
    /**
     * @var string db password
     */
    private $_password = '';
    /**
     * @var string db name
     */
    private $_databaseName = 'horse_racing_simulator';

    /**
     * DatabaseLibrary constructor.
     * The db connection is established in the private constructor.
     */
    private function __construct()
    {
        $connection = new PDO(
            "mysql:host={$this->_host};dbname={$this->_databaseName}", 
            $this->_user, 
            $this->_password,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
        );
        $this->_connection = $connection;
    }

    /**
     * get db instance
     * @return DatabaseLibrary
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new DatabaseLibrary();
        }

        return self::$_instance;
    }

    /**
     * get db pdo connection
     * @return PDO
     */
    public function getConnection()
    {
        return $this->_connection;
    }
}