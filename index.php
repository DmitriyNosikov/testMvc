<?php
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/core/include/prolog.php')) require_once($_SERVER['DOCUMENT_ROOT'].'/core/include/prolog.php');
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