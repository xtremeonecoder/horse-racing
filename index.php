<?php
/**
 * Horse Race Simulator
 *
 * @category   Application_Core
 * @package    horse-race-simulator
 * @author     Suman Barua
 * @developer  Suman Barua <sumanbarua576@gmail.com>
 */

$defaultMethod = 'index';
$defaultController = 'RaceController';
$protocal = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http");
$baseURL = "{$protocal}://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}/";

$applicationPath = DIRECTORY_SEPARATOR . 'application';
define('BASE_URL', $baseURL);
define('APPLICATION_PATH', __DIR__ . $applicationPath . DIRECTORY_SEPARATOR);

$modelPath = APPLICATION_PATH . 'models';
$viewPath = APPLICATION_PATH . 'views';
$controllerPath = APPLICATION_PATH . 'controllers';
$libraryPath = APPLICATION_PATH . 'libraries';

define('MODEL_PATH', $modelPath . DIRECTORY_SEPARATOR);
define('VIEW_PATH', $viewPath . DIRECTORY_SEPARATOR);
define('CONTROLLER_PATH', $controllerPath . DIRECTORY_SEPARATOR);
define('LIBRARY_PATH', $libraryPath . DIRECTORY_SEPARATOR);

/**
 * auto load classes
 */
spl_autoload_register(function ($className) {

    if (strpos($className, 'Controller')) {
        $filename = strtolower(preg_replace('/Controller$/', '', $className)).'.php';
        if (file_exists(CONTROLLER_PATH . $filename)) {
            include CONTROLLER_PATH . $filename;
        }
    }

    if (strpos($className, 'Model')) {
        $filename = strtolower(preg_replace('/Model$/', '', $className)).'.php';
        if (file_exists(MODEL_PATH . $filename)) {
            include MODEL_PATH . $filename;
        }
    }

    if (strpos($className, 'Library')) {
        $filename = strtolower(preg_replace('/Library$/', '', $className)).'.php';
        if (file_exists(LIBRARY_PATH . $filename)) {
            include LIBRARY_PATH . $filename;
        }
    }
});

// assign default values
$method = $defaultMethod;
$controller = $defaultController;

// find defined controller or method
$requestURI = explode("index.php", $_SERVER['REQUEST_URI']);
if(count($requestURI)>1 && !in_array($requestURI[1], array('', '/'))){
    // select controller
    $requestURI = explode('/', $requestURI[1]);
    $controller = str_replace(' ', '', ucwords(str_replace('-', ' ', $requestURI[1])));
    $controller = "{$controller}Controller";
    
    // select method
    if (isset($requestURI[2]) && !in_array($requestURI[2], array('', '/'))) {
        $method = $requestURI[2];
    }
}

try {
    // initialize controller class
    $controllerObject = new $controller();
} catch(Exception $e) {
    // method is not callable
    header('HTTP/1.0 404 Not Found');
    echo '404 Not Founds!';
    die;
}

// check for a valid method
if (!is_callable([$controllerObject, $method])) {
    // method is not callable
    header('HTTP/1.0 404 Not Found');
    echo '404 Not Founds!';
    die;
}

// invoke the selected method
if (isset($requestURI[3])) {
    // call method and pass parameters
    $params = array_slice($requestURI, 3);
    call_user_func_array(array($controllerObject, $method), $params);
} else {
    // call method
    $controllerObject->$method();
}