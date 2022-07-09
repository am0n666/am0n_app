<?php
declare(strict_types=1);

error_reporting(E_ALL);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');


try {
	include APP_PATH . '/config/loader.php';

	include APP_PATH . '/config/services.php';

	$config = $AppConfig->load();

	include_once(APP_PATH . '/views/index.phtml');
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}