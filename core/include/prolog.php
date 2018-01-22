<?php
/* /////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////// FRONT CONTROLLER /////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////*/

/* Общие настройки */
define('ROOT', $_SERVER["DOCUMENT_ROOT"]);

require_once(ROOT.'/core/config/siteconn.php'); //Подключение конфигурационного файла сайта

session_start(); //Старт сессии пользователя $_SESSION (!!!В дальнейшем, нужно стартовать, только если пользователь авторизовался)

/* Подключение системных классов с помощью функции-автозагрузчика */
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

/* Подключение всех основных классов */
// if(file_exists(ROOT."/core/classes/router.php")) require_once(ROOT."/core/classes/router.php");
if(file_exists(ROOT."/core/tools/functions.php")) require_once(ROOT."/core/tools/functions.php"); 

/* Установка соединения с БД */
require_once(ROOT.'/core/config/dbconn.php');

/* Вызов компонента Router */
if(class_exists('Core\Classes\Router'))
{
	$router = new Core\Classes\Router();

	$router->run();
}