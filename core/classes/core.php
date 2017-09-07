<?php
/* 
* Класс с основными функциями для ядра
* Пока представляет из себя сборную солянку. В дальнейшем планируется 
* раскидать разработанные в нем методы по отдельным соответствующим классам.
* ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
* Примерный перечень будущих классов:
* CFile - Все что касается файлов:
* -- Проверка валидности расширения
* -- Проверка файла на существование
* -- Создание, удаление, перемещение, переименование, редактирование прав на файл
* -- Создание, удаление и редактирование директорий
* -- Загрузка файлов на сервер
*
* CUser: - Все что связано с пользователями\
* -- Регистрация, авторизация
* -- Восстановление забытого пароля
* -- Хэширование пароля
* -- Авторизация по хэшу ("Фкнция Запомнить меня")
*
*/
namespace Core\Classes;

class Core
{			
	//Метод отправки почтовых сообщений
	/*
	* @arr $params - Массив с параметрами отправки вида:
		"FROM"=>Почта отправителя,
		"TO"=>Почта или массив с адресами почт, на которые необходимо отправить сообщение,
		"SUBJECT"=>Тема сообщения,
		"TYPE"=> text|htmp - тип сообщения обычный текст или html код,
		"ENCODING"=> utf-8|windows-1251 - кодировка сообещния
		"TEXT"=>Текст сообщения
	*/
	public static function send($params)
	{
		$from = $params['FROM'];
		
		if(is_array($params['TO']))
		{
			foreach($params['TO'] as $value)
			{
				$to .= " <".$value.">,";
			}
		}
		else $to = $params['TO'];
		
		// $subject = "=?utf-8?b?".base64_encode($params['SUBJECT'])."?=";
		$subject = "=?windows-1251??b?".base64_encode($params['SUBJECT'])."?=";
		$encoding = $params['ENCODING'];
		$message = $params['TEXT'];
		
		if(!$encoding) $encoding = 'windows-1251';
		
		$headers .= "From: <".$from	."> \r\n";
		$headers .= "Reply-To: ".$from	." \r\n";
		$headers .= "MIME-Version: 1.0" . "\r\n";
		$headers .= "X-Mailer: PHP/" . phpversion();
		
		if($params['TYPE'] == 'text') $headers .= "Content-type: text/plain; charset='".$params['ENCODING']."' \r\n";
		else if($params['TYPE'] == 'html') $headers .= "Content-type: text/html; charset='".$params['ENCODING']."' \r\n";
		
		return mail($to, $subject, $from, $message, $headers);
	}
	
	/* 
	* @description Функция получения хэша переданной строки
	* @param {string} @str  - Хэшируемая строка 
	* @ param {string} @salt  - Соль. Если ничего не передано, задается значение по умолчанию.
	* @return {string} Хэш переданной строки
	*
	*/
	public static function getHash($str, $salt="simple9Salt26")
	{
		if(function_exists("crypt")) return crypt($str, substr($str, 0, 2));	
		
		$str = $salt.$str.$salt;
		
		if(function_exists("sha1"))return sha1($str);	
		if(function_exists("md5"))return md5($str);	
	}
	/*
	* @description метод ответа на Ajax запрос в формате JSON
	* @param {array} @arr - Массив для дальнейшей передачи клиенту
	*/
	public static function resJSON($arr)
	{
		header('Content-Type: application/json');
    	echo json_encode($arr);
	}

	//@description Получение сформированного изображения капчи и ее строкового значения
	public static function getCaptchaImg()
	{			
	}
	
	//@description Проверка капчи
	public static function checkCaptcha()
	{
	}
}