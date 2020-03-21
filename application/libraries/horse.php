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
 * Class HorseLibrary
 */
class HorseLibrary
{
    /**
     * base speed of the horse
     * @var float
     */
    private $_baseSpeed = 5.0;
    /**
     * jockey effect on the hose speed
     * @var float
     */
    private $_jockeyEffect = 5.0;
    /**
     * horse ID
     * @var int
     */
    private $_horseID;
    /**
     * horse speed
     * @var float
     */
    private $_horseSpeed;
    /**
     * horse strength
     * @var float
     */
    private $_horseStrength;
    /**
     * horse endurance
     * @var float
     */
    private $_horseEndurance;
    /**
     * distance the horse covered
     * @var float
     */
    private $_distanceCovered = 0;
    /**
     * the horse finished the race (in seconds)
     * @var double
     */
    private $_elapsedTime;
    /**
     * position of the horse in the race
     * @var int
     */
    private $_horsePosition;

    /**
     * HorseLibrary constructor.
     * @param int $horseID
     * @param $horseSpeed
     * @param $horseStrength
     * @param $horseEndurance
     */
    public function __construct($horseID = null, $horseSpeed = null, $horseStrength = null, $horseEndurance = null)
    {
        if (is_null($horseID)) {
            // new horse
            $this->generateHorseStats();
        } else {
            // load horse
            $this->_horseID = $horseID;
            $this->_horseSpeed = $horseSpeed;
            $this->_horseStrength = $horseStrength;
            $this->_horseEndurance = $horseEndurance;
        }
        $this->timeToComplete();
    }

    /**
     * get distance covered by horse
     * @return float
     */
    public function getDistanceCovered()
    {
        return $this->_distanceCovered;
    }

    /**
     * get distance covered by horse (percentage)
     * @return float
     */
    public function getDistanceCoveredPercentage()
    {
        $raceController = new RaceController();
        return ($this->_distanceCovered * 100) / $raceController->_distanceOfRace;
    }
    
    /**
     * getter of horse elapsed time (in seconds)
     * @return float
     */
    public function getElapsedTime()
    {
        return $this->_elapsedTime;
    }

    /**
     * get horse speed
     * @return float
     */
    public function getSpeed()
    {
        return $this->_horseSpeed;
    }

    /**
     * get horse strength
     * @return float
     */
    public function getStrength()
    {
        return $this->_horseStrength;
    }

    /**
     * get horse endurance
     * @return float
     */
    public function getEndurance()
    {
        return $this->_horseEndurance;
    }

    /**
     * get horse id
     * @return int
     */
    public function getId()
    {
        return $this->_horseID;
    }

    /**
     * set horse id
     * @param int $horseID
     */
    public function setId($horseID)
    {
        $this->_horseID = $horseID;
    }

    /**
     * set final position in the race
     * @param $horsePosition
     */
    public function setPosition($horsePosition)
    {
        $this->_horsePosition = $horsePosition;
    }

    /**
     * get final position in the race
     * @return int
     */
    public function getPosition()
    {
        return $this->_horsePosition;
    }

    /**
     * speed after after finishing endurance
     * @return float
     */
    public function newSpeed()
    {
        return $this->bestSpeed() - $this->slowSpeedBy();
    }

    /**
     * how much should the horse slow by after finishing endurance
     * @return float
     */
    private function slowSpeedBy()
    {
        return $this->_jockeyEffect - ($this->_jockeyEffect * ($this->_horseStrength * 8 / 100));
    }

    /**
     * speed of the horse before finishing endurance
     * @return float
     */
    private function bestSpeed()
    {
        return $this->_baseSpeed + $this->_horseSpeed;
    }

    /**
     * how much will the horse travel using the endurance
     * @return float|int
     */
    private function calculateDistanceAtBestSpeed()
    {
        return $this->_horseEndurance * 100;
    }

    /**
     * how long will it take the horse to finish the race
     */
    private function timeToComplete()
    {
        //time it would take the horse to complete the race
        $raceController = new RaceController();
        $metersAtBestSpeed = $this->calculateDistanceAtBestSpeed();
        $elapsedTimeWithEndurance = $metersAtBestSpeed / $this->bestSpeed();
        $elapsedTimeWithoutEndurance = ($raceController->_distanceOfRace - $metersAtBestSpeed) / $this->newSpeed();
        $this->_elapsedTime = $elapsedTimeWithEndurance + $elapsedTimeWithoutEndurance;
    }

    /**
     * set distance covered by horse in specific time
     * @param $time
     */
    public function setDistanceCovered($time)
    {
        $timeAtEnduranceEnd = $this->calculateDistanceAtBestSpeed() / $this->bestSpeed();
        if ($timeAtEnduranceEnd >= $time) {
            // horse is still have endurance
            $distanceCovered = $this->distance(
                $this->bestSpeed(), 
                $time
            );
        } else {
            // horse finished the endurance
            $distanceCoveredWithEndurance = $this->distance(
                $this->bestSpeed(), 
                $timeAtEnduranceEnd
            );
            $distanceCoveredWithoutEndurance = $this->distance(
                $this->newSpeed(), 
                $time - $timeAtEnduranceEnd
            );
            $distanceCovered = $distanceCoveredWithEndurance + $distanceCoveredWithoutEndurance;
        }
        
        $raceController = new RaceController();
        if ($distanceCovered >= $raceController->_distanceOfRace) {
            // horse finished the race
            $distanceCovered = $raceController->_distanceOfRace;
        }
        $this->_distanceCovered = $distanceCovered;
    }

    /**
     * calculate distance
     * @param $speed
     * @param $time
     * @return float
     */
    private function distance($speed, $time)
    {
        return $speed * $time;
    }

    /**
     * generate random number between 0.0 to 10.0
     * @return float|int
     */
    private function generateRandom()
    {
        return rand(0, 100) / 10;
    }

    /**
     * set horse stats
     */
    private function generateHorseStats()
    {
        $horseSpeed = $this->generateRandom();
        $horseStrength = $this->generateRandom();
        if ($horseSpeed == 0 && $horseStrength == 0) {
            // make sure not to do division by zero
            return $this->generateHorseStats();
        }
        $this->_horseSpeed = $horseSpeed;
        $this->_horseStrength = $horseStrength;
        $this->_horseEndurance = $this->generateRandom();
    }

    /**
     * check if the horse finished the race
     * @return bool
     */
    public function finishRace(){
        $raceController = new RaceController();
        return $this->getDistanceCovered() == $raceController->_distanceOfRace;
    }
}