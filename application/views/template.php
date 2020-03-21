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
?>

<!-- HTML5 Templating Format -->

<!DOCTYPE HTML>
<html>
    <head>
        <title>Horse Race Simulator</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <nav class="navbar navbar-dark bg-dark sticky-top">
                <button id="createRace" class="btn btn-primary mr-2" type="button">Create Race</button>
                <h4 class="text-white font-weight-bold">~: Horse Racing Simulator :~</h4>
                <button id="progressRace" disabled="disabled" class="btn btn-success" type="button">Progress Race</button>
            </nav>
            <div id="bodyContents">
                <?php include(VIEW_PATH . $contentView . '.php'); ?>
            </div>
        </div>
        <br />
        <footer class="container border-top text-center py-3">
            <p>
                Copyright &copy; <?php echo date("Y", time()); ?>,
                <a target="_blank" href="https://github.com/xtremeonecoder/">XtremeOneCoder</a>
                by <a target="_blank" href="https://www.linkedin.com/in/xtreme1coder/">Suman Barua</a>.
            </p>
        </footer>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script type="text/javascript">
            const $appBaseUrl = '<?php echo BASE_URL; ?>';
            const $canProgress = <?php echo isset($data['races'][0]) ? 1 : 0; ?>;
        </script>
        <script src="./public/js/script.js" type="text/javascript"></script>
    </body>
</html>