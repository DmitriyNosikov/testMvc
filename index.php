<?php
/* //////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////FRONT CONTROLLER /////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////*/

/* Общие настройки */

//-- Объявление системных констант
define("ROOT_DIR", $_SERVER["DOCUMENT_ROOT"]);
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
if(file_exists(ROOT_DIR."/core/classes/router.php")) require_once(ROOT_DIR."/core/classes/router.php");


/* Установка соединения с БД */

/* Вызов компонента Router */
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Test MVC project</title>
</head>
<body>
<?
	$router = new Router();
	
	$router->run();

	/* Рабочая область */
	function dump($val, $die = false)
	{
		echo "<pre style='display: block; background: #ccc; padding: 10px; border-radius: 5px;'>";
		print_r($val);
		echo "</pre>";
		
		if($die) die();
	};
?>
</body>
</html>