<?php
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/core/include/prolog.php')) require_once($_SERVER['DOCUMENT_ROOT'].'/core/include/prolog.php');
/* Тестовый блок */

$str = 'Он учился в вузе с 2000 по 2017 год';
$pattern = '#2017#';
dump(preg_match($pattern, $str));
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="/core/blocks/css/main/main.css">
	<title>Test MVC project</title>
</head>
<body>
	<span class="error-block has-error">it`s success</span><br>
	<span class="success-block has-success">it`s error</span>
</body>
</html>