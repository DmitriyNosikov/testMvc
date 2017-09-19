<?php
/* /////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////// FRONT CONTROLLER /////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////*/

/* Общие настройки */
setlocale('LC_ALL', ''); //Установка локали для строковых функций

define('ROOT', $_SERVER["DOCUMENT_ROOT"]);

//-- Объявление системных констант
require_once(ROOT.'/core/config/siteconn.php');

session_start(); //Старт сессии пользователя $_SESSION (!!!В дальнейшем, нужно стартовать, только если пользователь авторизовался)

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
require_once(ROOT.'/core/config/dbconn.php');

/* Вызов компонента Router */
if(class_exists('Core\Classes\Router'))
{
	$router = new Core\Classes\Router();

	$router->run();
}