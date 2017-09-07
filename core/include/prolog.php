<?php
	/* /////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////// FRONT CONTROLLER /////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////*/

/* Общие настройки */

//-- Объявление системных констант
define("ROOT", $_SERVER["DOCUMENT_ROOT"]);
define("SITE_LANG", "ru"); //ru, en
define("DISPLAY_ERRORS", true);

//-- Включение/Выключение ошибок
if(defined(DISPLAY_ERRORS) && DISPLAY_ERRORS == true)
{
	ini_set("display_errors", 1);
	//0 - Отключение ошибок, E_ALL - Все ошибки. Константы для данной функции http://php.net/manual/ru/errorfunc.constants.php	
	ini_set("error_reporting", E_ALL); 
}
else
{
	ini_set("display_errors", 0);
	ini_set("error_reporting", 0); 
}

/* Подключение системных файлов */
spl_autoload_register(function($className){	
	$className = ltrim($className, '\\');
	$fileName  = '';
	$namespace = '';

	if ($lastNsPos = strrpos($className, '\\')) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
		}

	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	if(file_exists(ROOT.'/'.$fileName)) require_once(ROOT.'/'.$fileName);
});

// if(file_exists(ROOT."/core/classes/router.php")) require_once(ROOT."/core/classes/router.php");
if(file_exists(ROOT."/core/tools/functions.php")) require_once(ROOT."/core/tools/functions.php");

/* Установка соединения с БД */

/* Вызов компонента Router */
if(class_exists('Core\Classes\Router'))
{
	$router = new Core\Classes\Router();

	$router->run();
}
