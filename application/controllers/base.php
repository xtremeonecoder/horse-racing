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
 * Class BaseController
 */
class BaseController
{
    /**
     * load view file
     * @param string $contentView the page view
     * @param mixed $data the data the view will show
     */
    public function loadView($contentView, $data = null, $ajax = false)
    {
        if($ajax){
            return include(VIEW_PATH . $contentView . '.php');
        }else{
            include(VIEW_PATH . 'template.php');
        }
    }

}