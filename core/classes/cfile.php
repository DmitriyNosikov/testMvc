<?php
/* 
* TODO: Допилить метод загрузки файлов uploadFile
*/

namespace Core\Classes;

class CFile
{
	protected $allExts = array(); //Массив со всеми допустимыми расширениями файлов
	public $enable_error = true; //Включить/выключить вывод ошибок
	
	function __construct()
	{
		$this->allExts['VIDEO'] = array('mp4', 'wmf', 'avi', 'mpeg', 'vob', 'ogg', '3gp', 'flv', 'mpv', 'mpv2', 'mpv4', 'webm');
		$this->allExts['AUDIO'] = array( 'mp1', 'mp2', 'mp3', 'mp4', 'wma', 'ogg', 'wav', 'vox', 'raw', 'vgm');
		$this->allExts['DOC'] = array('pdf', 'doc', 'docx', 'txt', 'odt', 'sxw', 'djv', 'fb2', 'fb3', 'xls',  'xlsx', 'xlsm', 'sxc');
		$this->allExts['IMAGE'] = array('jpg', 'jpeg', 'png', 'gif', 'tiff', 'pdf', 'bmp', 'svg', 'pdf', 'cdr');
		$this->allExts['ARCHIVE'] = array('rar', 'tar', 'dar', 'zip', 'gzip', '7z');
	}
	
	/*
	* @description Метод транслитерации русских строк
	* @param {string} Строка на русском языке
	*/
	public function rus2Translit($str)
	{
		if($str)
		{
			$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'yo',  'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => '',    'ы' => 'y',   'ъ' => '',
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'Yo',  'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
			' '=>'_', '('=>'-', ')'=>'-', '['=>'_', ']'=>'_', 
			'"'=>'_', '\''=>'_',
			);
			return strtr($str, $converter);
		}
	}
	
	/*
	* @description Метод перевода с транслита на русский язык
	* @param {string} Строка на транслите
	*/
	function translit2rus($str)
	{
		$converter = array(
			'q' => 'й', 'w' => 'ц', 'e' => 'у',
			'r' => 'к', 't' => 'е', 'y' => 'н',
			'u' => 'г', 'i' => 'ш', 'o' => 'щ',
			'p' => 'з', '[' => 'х', ']' => 'ъ',
			'a' => 'ф', 's' => 'ы', 'd' => 'в',
			'f'=> 'а', 'g' => 'п', 'h' => 'р',
			'j' => 'о', 'k' => 'л', 'l' => 'д',
			';' => 'ж', '\'' => 'э', 'z' => 'я',
			'x' => 'ч', 'c' => 'с', 'v' => 'м',
			'b' => 'и', 'n' => 'т', 'm' => 'ь',
			',' => 'б', '.' => 'ю',
			
			'Q' => 'й', 'W' => 'ц', 'E' => 'у',
			'R' => 'к', 'T' => 'е', 'Y' => 'н',
			'U' => 'г', 'I' => 'ш', 'O' => 'щ',
			'P' => 'з', '[' => 'х', ']' => 'ъ',
			'A' => 'ф', 'S' => 'ы', 'D' => 'в',
			'F'=> 'а', 'G' => 'п', 'H' => 'р',
			'J' => 'K', 'K' => 'л', 'L' => 'д',
			';' => 'ж', '\'' => 'э', 'Z' => 'я',
			'X' => 'ч', 'C' => 'с', 'V' => 'м',
			'B' => 'и', 'N' => 'т', 'M' => 'ь',
			',' => 'б', '.' => 'ю',
		);
		return strtr($str, $converter);
	}

	/* 
	* Метод проверки допустимого расширения файла
	* @param {string} $fullFileName - Полное имя файла
	* @param {array} $addExt - Дополнительный массив расширений, который хотел бы разрешить к загрузке юзер
	*/
	public function checkFileExt($fullFileName, $addExt=array())
	{
		$extArray = $this->allExts;
		$checkType = false;
		
		$fileExt = $this->getFileExt($fullFileName);
		
		foreach($extArray  as $key => $value)
		{
			if(in_array($fileExt, $value))
			{
				$checkType = true;
				break;
			}

			//Дополнительная проверка пользовательских расширений
			if(is_array($addExt) && !empty($addExt) && in_array($fileExt, $addExt))
			{
				$checkType = true;
				break;
			}
		}
		
		return $checkType;
	}
	
	/*
	* @description Метод для получения расширения файла
	* @param {string} $fullFileName - Полное имя файла, включая расширение (image.jpg | archive.rar)
	*/
	public function getFileExt($fullFileName)
	{
		if(empty($fullFileName))
		{
			$this->showError('Методу getFileExt($fullFileName) необходимо передать строковый параметр $fullFileName(имя файла с расширением)');
			return false;
		}
		
		$ext_start_pos = strrpos($fullFileName, '.');
		
		if(empty($ext_start_pos))
		{
			$this->showError('Невозможно получить расширение файла '.$fullFileName.'. Возможно, переданное имя файла не содержит расширения.');
			return false;
		}
		
		//+1 для того, чтобы выводить расширение без точки
		$fileExt = substr($fullFileName, $ext_start_pos+1);
		$fileExt = strtolower($fileExt);
		
		return $fileExt;
	}
	
	//@description Метод для вывода всех доступных для загрузки расширений
	public function getAllowedFileExt()
	{
		$extArray = $this->allExts;
		echo "Для загрузки разрешены следующие типы файлов: ";
		
		foreach($extArray as $key => $value)
		{
			echo $key.": ";
			foreach($value as $extensions)
			{
				echo " | ".$extensions;
			}
			echo "<br>";
		}
	}
	
	/*
	* @description Метод для получения типа загружаемого файла
	* @param {string} $fullFileName - Полное имя файла, включая расширение (image.jpg | archive.rar)
	* @return {string} Тип файла (image, audio, video, document, archive)
	*/
	public function getFileType($fullFileName)
	{
		$fileExt = $this->getFileExt($fullFileName);

		echo "Расширение ".$fileExt."<br>";
		
		if(!empty($fileExt))
		{
			$extArray = $this->allExts;
			
			foreach($extArray as $key => $value)
			{
				if(in_array($fileExt, $value)) return $key;
			}
		}
		
		$this->showError('Имя переданного файла, переданного методу getFileType($fullFileName) должно содержать его расширение. Пример: image.jpg');
		return false;
	}
	
	/* 
	* @description Метод, возвращающий новое имя файла
	* @param {string} $filename - Имя файла с расширением
	* @param {bool} $rename - Флаг, указывающий, переименовывать ли файл или оставлять
	* его исходное имя, добавляя уникальынй индекс
	*/
	public function getNewFileName($filename, $rename=false)
	{			
		if(empty($filename) || gettype($filename) !== 'string')
		{
			$this->showError('Методу getNewFileName($filename, $rename) необходимо обязательно передать строковый параметр $filename (имя файла) ');
			return false;		
		}
		
		$unique_index = "_".time().rand(0, 999);
		$file_ext = $this->getFileExt($filename);
		$filename_len = $this->getFileNameLen($filename);
		
		if(!empty($file_ext))
		{
			//$clear_filename - Имя файла без расширения
			$clear_filename = substr($filename, 0, $filename_len);
			$unique_index = $unique_index.".".$file_ext;
		}	
		
		if($rename)
		{
			$file_type = $this->getFileType($filename);
			return $file_type.$unique_index;
		}
		else return $clear_filename.$unique_index;
	}
	
	/*
	* @description Метод для получения длины имени файла
	* @param {string} Имя файла
	*/
	public function getFileNameLen($filename)
	{
		if(empty($filename) || gettype($filename) !== 'string')
		{
			$this->showError('Методу getFileNameLen($filename) необходимо обязательно передать строковый параметр $filename (имя файла)');
			return false;		
		}
		
		$file_ext = $this->getFileExt($filename);
		
		if(!empty($file_ext))
		{
			$ext_len = strlen($file_ext)+1;
			$filename_len = strlen($filename)-$ext_len;
			
			return $filename_len;
		}
		else return strlen($filename);
	}
	
	/* 
	* @description Метод очистки имени файла от лишних символов
	* @param {string} - Имя файла
	*/
	public function clearFileName($filename)
	{
		if(!empty($filename))
		{			
			$filename = $this->rus2Translit($filename);
			$filename = htmlspecialchars($filename);
			$filename = preg_replace('/[^a-zа-я\d_\.]*/i', "", $filename);
			$filename = iconv("UTF-8", "UTF-8//IGNORE", $filename); 
			
			return $filename;
		}
		
		$this->showError('Методу clearFileName($filename) необходимо передать стоковый параметр $filename (имя файла)');
		return false;
	}
	
	/* 
	* @description Метод, проверяющий наличие файла в директории
	* @param {string} $filenam - Имя файла
	* @param {string} $filedir - Проверяемая директория
	*/
	public function checkFileInDir($filename, $filedir)
	{
		$scan_dir = scandir($filedir);
		
		if(in_array($filename,$scan_dir)) return true;
		
		return false;
	}
	
	//@description Удаление файла
	public static function deleteFile($src)
	{
		if(file_exists($src))
		{
			unlink($src);
			return true;
		}
		
		return false;
	}
	
	//@description Удаление директории
	public static function deleteDir($dir)
	{
		if(!is_readable($dir)) return false;
		$files = scandir($dir);
		
		foreach($files as $f) 
		{
			if($f == '.' || $f == '..') continue;
			
			if(is_dir($dir.'/'.$f)) $this->deleteDir($dir.'/'.$f);
			else unlink($dir.'/'.$f);
		}
		rmdir($dir);
	}
	
	//Второй вариант метода-удаления директории
	public function delDir($dir)
	{
		$list = glob($dir."/*"); //Получаем список всех файлов и каталогов в каталоге $dir

		//Перебираем в цикле массив с файлами директории
		//Если текущий файл-директория, вызываем функцию снова
		//Иначе - удаляем файл. В самом конце удаляем переанную дирекорию
		foreach($list as $file)
		{
			//Очищаем от двойных слешей
			$file = str_replace("//", "/", $file);
			
			if (is_dir($file)) $this->delDir($file);
			elseif(file_exists($file))unlink($file);
		}
		rmdir($dir);
	}
	
	/*
	* @description Метод загрузки файлов на сервер
	* @param {array} $filesArr - Массив $_FILES
	* @param {string} $filesPath - Директория загрузки файлов. С начальным и замыкающим слешем (Прим.: /upload/)
	*/
	function uploadFiles($filesArr, $filesPath)
	{

		$uloadFilesError = array();
		$filesDir = $_SERVER['DOCUMENT_ROOT'].$filesPath;

		if(empty($filesArr) || empty($filesPath) || !is_dir($filesDir)) return false;

		$filesFieldName = key($filesArr); //Получаем ключ(название поля) из переданного массива $_FILES

		if(is_array($filesArr[$filesFieldName]['name']))
		{
			foreach($filesArr[$filesFieldName]['name'] as $key => $value)
			{
				$fileName = $filesDir.basename($filesArr[$filesFieldName]['name'][$key]);
				$resUpload = @move_uploaded_file($filesArr[$filesFieldName]['tmp_name'][$key], $fileName);

				if(!$resUpload) $uloadFilesError[] = "Ошибка при загрузке файла ".$filesArr[$filesFieldName]['name'][$key];
			}
		}
		else
		{
			$fileName = $filesDir.basename($filesArr[$filesFieldName]['name']);
			$resUpload = @move_uploaded_file($filesArr[$filesFieldName]['tmp_name'], $fileName);

			if(!$resUpload) $uloadFilesError[] = "Ошибка при загрузке файла ".$filesArr[$filesFieldName]['name'];
		}

		if(empty($uloadFilesError)) return true;
		else return false;
	}
	
	
	/* 
	* Генерация ошибки
	* var @str - строка ошибки
	*/
	public function showError($str = false)
	{
		if(!$this->enable_error) return false;
		
		echo "<br>";

		if($str) echo $str;
		else echo "В процессе работы с методом класса возникли ошибки. Пожалуйста, проверьте корректность переданных параметров.";
		
		echo "<br>";
	}
}