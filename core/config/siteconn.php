<?php
define('ROOT', $_SERVER["DOCUMENT_ROOT"]);
define('DISPLAY_ERRORS', true);
define('LANG_ID', 'ru'); //ru, en
define('SITE_CHARSET', 'UTF-8');
define('DATE_FORMAT', 'd.m.Y');
define('DATETIME_FORMAT', 'd.m.Y H:i:s');

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
}

@ini_set('memory_limit', '512M');