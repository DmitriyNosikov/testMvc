<?php
setlocale('LC_ALL', ''); //Установка стоковой локали для строковых функций
date_default_timezone_set('Europe/Moscow'); //Установка дефолтнйо временной зоны

define('DISPLAY_ERRORS', true);
define('LANG_ID', 'ru'); //ru, en
define('SITE_CHARSET', 'UTF-8');
define('DATE_FORMAT', 'd.m.Y');
define('DATETIME_FORMAT', 'd.m.Y H:i:s');

//http://php.net/manual/ru/function.chmod.php
define('FILE_PERMISSION', 0644);
define('DIR_PERMISSION', 0755);
@umask(~DIR_PERMISSION);

define('LOG_FILENAME', 'srv_log.txt');

@ini_set('default_charset', SITE_CHARSET);
@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', '30');

//-- Включение/Выключение ошибок
if(defined('DISPLAY_ERRORS') && DISPLAY_ERRORS == true)
{
	ini_set('display_errors', 1);
	//0 - Отключение ошибок, E_ALL - Все ошибки. Константы для данной функции http://php.net/manual/ru/errorfunc.constants.php	
	ini_set('error_reporting', E_ALL); 
}
else
{
	ini_set('display_errors', 0);
	ini_set('error_reporting', 0);
	ini_set('error_log', '/core/logs/error-log.txt');
	ini_set('log_errors', 0);
}
