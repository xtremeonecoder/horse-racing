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

use HorseLibrary as Horse;

/**
 * Class RaceController
 */
class RaceController extends BaseController
{
    /**
     * how many horses in a single race
     */
    public $_horsesPerRace = 8;

    /**
     * max number of running races
     */
    public $_maxActiveRaces = 3;

    /**
     * race distance
     */
    public $_distanceOfRace = 1500;

    /**
     * progress race per click
     */
    public $_progressPerClick = 10;

    /**
     * horse race simulation landing page
     * this will load all required data for the the home page view
     */
    public function index($ajax = false)
    {
        $data = array();
        $raceModel = RaceModel::getInstance();
        $activeRaces = $raceModel->getActiveRaces();
        $this->getRaceHorses($activeRaces);
        $data['races'] = $activeRaces;
        $data['last_five_races'] = $raceModel->getLatest(5);
        $this->getRaceHorses($data['last_five_races'], true);
        $horseModel = HorseModel::getInstance();
        $data['top_horse'] = $horseModel->getTopHorse();
        
        // load view for contents
        if($ajax){
            // load view for ajax contents
            $data['html_contents'] = $this->loadView('race-ajax', $data, $ajax);
            return $data;
        }else{
            // load view for non-ajax contents
            $this->loadView('race', $data);
        }
    }

    /**
     * create a new race and generate its horses if possible
     */
    public function createRace()
    {
        $raceModel = RaceModel::getInstance();
        $activeRaces = $raceModel->getActiveRaces();
        if (count($activeRaces) < $this->_maxActiveRaces) {
            $race = array();
            $horseModel = HorseModel::getInstance();
            // race progress time
            $race['race_progress_time'] = 0;
            // race finished
            $race['race_finished'] = false;
            // race finish time
            $race['race_finish_time'] = 0;

            $horses = array();
            // generate 8 horses with random stats
            for ($i = 0; $i < $this->_horsesPerRace; $i++) {
                $horse = new Horse();
                if ($horse->getElapsedTime() > $race['race_finish_time']) {
                    // we already know who will finish first so what is the point in not saving it now to the DB
                    $race['race_finish_time'] = $horse->getElapsedTime();
                }
                $horses[] = $horse;
            }
            $raceID = $raceModel->insert($race['race_finish_time']);
            if ($raceID === false) {
                error_log('ERROR - create race failed while trying to insert a new race');
            } else {
                $race['race_id'] = $raceID;
                //save horses
                $horseIDs = $horseModel->insertMultiple($raceID, $horses);
                if ($horseIDs < count($horses)) {
                    error_log('ERROR - not all horses where inserted properly!');
                }
            }
        }    
        
        // print json content
        $this->getJsonContent();
    }

    /**
     * progress the races
     */
    public function progress()
    {
        $raceModel = RaceModel::getInstance();
        $activeRaces = $raceModel->getActiveRaces();
        $this->getRaceHorses($activeRaces);
        foreach ($activeRaces as $key => $race) {
            // race still going
            $race['race_progress_time'] = $race['race_progress_time'] + $this->_progressPerClick;
            foreach ($race["horses"] as $key2 => $horse) {
                if ($horse->finishRace()) {
                    continue;
                }
                $horse->setDistanceCovered($race['race_progress_time']);
                if ($horse->finishRace()) {
                    // this horse finished the race
                    $race["finishers"]++;
                }
            }
            if ($race["finishers"] == count($race["horses"])) {
                //race ended now
                $race['race_finished'] = $raceModel->raceFinished();
            }
            $params = array(
                'race_id' => $race['race_id'],
                'race_progress_time' => $race['race_progress_time'],
                'race_finished' => $race['race_finished']
            );
            $result = $raceModel->update($params);
            if (!$result) {
                error_log('ERROR - update race failed!');
            }
        }
        
        // print json content
        $this->getJsonContent();
    }

    /**
     * get json content from index
     */
    private function getJsonContent()
    {
        // fetch contents for ajax call
        $result = $this->index(true);
        $ajaxData = array(
            'canCreate' => 0, 
            'canProgress' => 0,
            'htmlContent' => $result['html_contents']
        );
        
        // check create race button is enabled or disabled
        if (!isset($result['races'][0]) || 
                count($result['races']) < $this->_maxActiveRaces) {
            $ajaxData['canCreate'] = 1;
        }
        
        // check progress race button is enabled or disabled
        if (isset($result['races'][0])) {
            $ajaxData['canProgress'] = 1;
        }        
        
        // print json data
        echo json_encode($ajaxData);        
    }    
    /**
     * get race horses
     * @param $races
     * @param bool $topThree
     */
    private function getRaceHorses(&$races, $topThree = false)
    {
        $horseModel = HorseModel::getInstance();
        foreach ($races as $key => $race) {
            $race["finishers"] = 0;
            $race["position"] = array();
            $horses = array();
            if ($topThree) {
                $horsesResult = $horseModel->getTopHorsesByRaceID($race['race_id'], 3);
            } else {
                $horsesResult = $horseModel->getHorsesByRaceID($race['race_id']);
            }
            foreach ($horsesResult as $key1 => $horse) {
                $horseLibrary = new Horse(
                    $horse['horse_id'], 
                    $horse['horse_speed'], 
                    $horse['horse_strength'], 
                    $horse['horse_endurance']
                );
                $horseLibrary->setDistanceCovered($race['race_progress_time']);
                if ($horseLibrary->getElapsedTime() > $race['race_finish_time']) {
                    // we already know who will finish first so what is the point in not saving it now to the DB
                    $race['race_finish_time'] = $horseLibrary->getElapsedTime();
                }
                //final position
                $horseLibrary->setPosition(($key1 + 1));
                //current position
                $currentDistanceCovered = $horseLibrary->getDistanceCovered();
                $race["current_position"]['horse_' . $horseLibrary->getId()] = $currentDistanceCovered;
                if ($currentDistanceCovered == $this->_distanceOfRace) {
                    $race["finishers"]++;
                }
                $horses[] = $horseLibrary;
            }
            arsort($race["current_position"]);
            // resort them by id
            usort($horses, function($a, $b) {
                if($a->getId() == $b->getId()){return 0;}
                elseif($a->getId() <= $b->getId()){return -1;}
                elseif($a->getId() > $b->getId()){return 1;}
            });
            $race['horses'] = $horses;
            $races[$key] = $race;
        }
    }
}