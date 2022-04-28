<?php

use Phalcon\Di\FactoryDefault;
use App\Components\Curlapi;
use Phalcon\Mvc\Application;
use Phalcon\Events\Manager;



// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

$di = new FactoryDefault();

include APP_PATH.'/config/router.php';

include APP_PATH.'/config/services.php';

$config = $di->getConfig();

include APP_PATH.'/config/loader.php';

// include 'header.php';

$di->set('api', new Curlapi);

$eventsManager = new Manager();
$eventsManager->attach('token', new \App\Handler\Eventhandler());
$di->set('EventsManager', $eventsManager);

$application = new Application($di);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();

   
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}