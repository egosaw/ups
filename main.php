<?php 

set_time_limit(0);
error_reporting(E_ALL);

date_default_timezone_set('Europe/Moscow');

if (!ini_set('default_charset', 'utf-8'))
	echo "Could not set default_charset to utf-8".PHP_EOL;

$path    = dirname(__FILE__);

$htmldom = $path.'/lib/simple_html_dom.php';
$base    = $path.'/classes/Base.php';
$shoose  = $path.'/classes/Ego.php';
$config  = $path.'/config/main.php';

defined('EGO_DEBUG') or define('EGO_DEBUG',true);

require $htmldom;
require $base;
require $shoose;

// Create application
//Ego::createApplication($config)->run('major-expert', false, 3);
//Ego::createApplication($config)->run('ascgroup', false, 3);
//Ego::createApplication($config)->run('autonissan', false, 3);
//Ego::createApplication($config)->run('infiniti-asc', false, 3);
//Ego::createApplication($config)->run('kia-asc', false, 3);
Ego::createApplication($config)->run('audi-taganka', false, 3);