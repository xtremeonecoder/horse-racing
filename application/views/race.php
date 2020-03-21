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

// get instance of race-controller
$raceController = new RaceController();
?>

<br />
<?php if (isset($data['top_horse']['horse_id'])) { ?>
    <div class="page-header">
        <h4>Statistics of the horse that achieved best time</h4>
    </div>
    <div class="table-responsive">    
        <table class="table table-sm table-hover table-bordered table-striped">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th scope="col">Horse ID</th>
                    <th scope="col">Horse Speed</th>
                    <th scope="col">Horse Strength</th>
                    <th scope="col">Horse Endurance</th>
                    <th scope="col">Elapsed Time</th>
                </tr>
            </thead>    
            <tbody>
                <tr class="text-center">
                    <td><?php echo $data['top_horse']['horse_id'] ?></td>
                    <td><?php echo $data['top_horse']['horse_speed'] ?></td>
                    <td><?php echo $data['top_horse']['horse_strength'] ?></td>
                    <td><?php echo $data['top_horse']['horse_endurance'] ?></td>
                    <td><?php echo gmdate("H:i:s", $data['top_horse']['elapsed_time']) ?></td>
                </tr>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php if (isset($data['races'][0])) { ?>    
    <div class="page-header"><h4>Active Horse Races</h4></div>
    <?php foreach ($data['races'] as $race) { ?>
        <div class="page-header">
            <h5 class="text-center">
                Race ID: <?php echo $race['race_id']; ?>,
                Progress Time: <?php echo gmdate("H:i:s", $race['race_progress_time']); ?>
            </h5>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th scope="col" class="col-1 text-center">Horse ID</th>
                        <th scope="col" class="col-1">Position</th>
                        <th scope="col">Racing Ground (<?php echo $raceController->_distanceOfRace; ?> Meters)</th>
                        <th scope="col" class="col-2">Elapsed Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($race['horses'] as $horse) { ?>
                    <tr class="text-center">
                        <th scope="row"><?php echo $horse->getId() ?></th>
                        <td><?php echo $race['race_progress_time'] > 0 ? 1 + array_search('horse_' . $horse->getId(), array_keys($race["current_position"])) : 0 ?></td>
                        <td class="align-middle">
                            <?php 
                            $progressClass = "bg-danger";
                            $percentage = $horse->getDistanceCoveredPercentage(); 
                            if($percentage >= 95) {$progressClass = "bg-success";}
                            elseif($percentage >= 65 && $percentage < 95) {$progressClass = "bg-warning";}
                            ?>
                            <div class="progress">
                                <div class="progress-bar progress-bar-animated progress-bar-striped <?php echo $progressClass; ?>" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo round($horse->getDistanceCovered(), 2); ?>
                                </div>
                            </div>
                        </td>
                        <td><?php echo $horse->finishRace() ? gmdate("H:i:s", $horse->getElapsedTime()) : '' ?></td>
                    </tr>
                <?php } ?>
                </tbody>    
            </table>
        </div>
    <?php } ?>
<?php } ?>
    
<?php if (isset($data['last_five_races'][0])) { ?>
    <div class="page-header"><h4>Results of last five races</h4></div>
    <?php foreach ($data['last_five_races'] as $race) { ?>
        <div class="page-header">
            <h5 class="text-center">
                Race ID: <?php echo $race['race_id']; ?>,
                Finished Time: <?php echo $race['race_finished'] 
                        ? gmdate("H:i:s", $race['race_finish_time']) 
                        : gmdate("H:i:s", $race['race_progress_time']); ?>
            </h5>
        </div>
        <div class="table-responsive">    
            <table class="table table-sm table-hover table-bordered table-striped">
                <thead class="thead-dark">
                    <tr class="text-center">
                        <th scope="col" class="col-1">Horse ID</th>
                        <th scope="col" class="col-1">Position</th>
                        <th scope="col">Racing Ground (<?php echo $raceController->_distanceOfRace; ?> Meters)</th>
                        <th scope="col" class="col-2">Elapsed Time</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($race['horses'] as $horse) { ?>
                    <tr class="text-center">
                        <th scope="row"><?php echo $horse->getId(); ?></th>
                        <td><?php echo $horse->getPosition(); ?></td>
                        <td class="align-middle">
                            <?php $percentage = $horse->getDistanceCoveredPercentage(); ?>
                            <div class="progress">
                                <div class="progress-bar progress-bar-animated progress-bar-striped bg-success" role="progressbar" style="width: <?php echo $percentage; ?>%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                    <?php echo round($horse->getDistanceCovered(), 2); ?><i class="fas fa-horse"></i>
                                </div>
                            </div>
                        </td>
                        <td><?php echo gmdate("H:i:s", $horse->getElapsedTime()); ?></td>
                    </tr>
                <?php } ?>
                </tbody>    
            </table>
        </div>    
    <?php } ?>
<?php } ?>