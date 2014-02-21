<?php
if (!defined('ROOT_PATH'))  exit('ROOT_PATH UNDEFINED');
header("Content-type: text/html; charset=utf-8");
header("cache-control:no-cache,must-revalidate");
date_default_timezone_set('Asia/Shanghai');
define('SYS', dirname(ROOT_PATH));
define('LIB', SYS . "/library");
define('MODEL', SYS . "/model");
define('ENTITY', SYS . "/entity");
define('CONF', ROOT_PATH . "/conf");
define('LOG_PATH', ROOT_PATH . "/logs");
define('APP_PATH', ROOT_PATH . "/app");
define('CLIENT_LOG_PATH', "/home/logs/http/client_log");
define('DOWNLOAD_LOG_PATH', "/home/logs/http/download_log");

require(SYS.'/comm/comm.php');
require(ROOT_PATH.'/comm/comm.php');

import('config');
$debug = Config::get('debug', 'system', null, false);
if ($debug)
{
	error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
	ini_set('display_errors', 1);
}
else
{
	error_reporting(0);
	ini_set('display_errors', 0);
}
set_time_limit(0);
ini_set('memory_limit','100M');
import('http');