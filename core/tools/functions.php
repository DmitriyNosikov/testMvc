<?php
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL); 

//@description Вывод дампа переменной $val
function dump($val, $die = false)
{
    echo '<pre>';
    print_r($val);
    echo '</pre>';

    if($die)
    {
        die();
    }
}

//Подумать, как сделать форматированную запись массива в файл + добавлять бэктрейс
function add2Log($text)
{
    if(defined('LOG_FILENAME'))
    {
        $logFile = @fopen(LOG_FILENAME, 'a+');

        if($logFile && flock($logFile, LOCK_EX))
        {
            ignore_user_abort(true);

            @fwrite($logFile, 'Host: '.$_SERVER['HTTP_HOST'].PHP_EOL.'Date: '.date('d.m.Y H:i:s').PHP_EOL.$text.PHP_EOL);
            @fwrite($logFile, '-----------------'.PHP_EOL);
            @fwrite($logFile, 'Stacktrace: '.print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true).PHP_EOL);

            @fflush($logFile);
            @flock($logFile, LOCK_UN);
            @fclose($logFile);
            
            ignore_user_abort(false);
        }
    }
}

//@description Транслитерация строки
function rus2translit($str)
{
    $converter = array(
        'а' => 'a',   'б' => 'b',   'в' => 'v',
        'г' => 'g',   'д' => 'd',   'е' => 'e',
        'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
        'и' => 'i',   'й' => 'y',   'к' => 'k',
        'л' => 'l',   'м' => 'm',   'н' => 'n',
        'о' => 'o',   'п' => 'p',   'р' => 'r',
        'с' => 's',   'т' => 't',   'у' => 'u',
        'ф' => 'f',   'х' => 'h',   'ц' => 'c',
        'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
        'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
        'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
        ' ' => '-', '"' => '',

        'А' => 'a',   'Б' => 'b',   'В' => 'v',
        'Г' => 'g',   'Д' => 'd',   'Е' => 'e',
        'Ё' => 'e',   'Ж' => 'zh',  'З' => 'z',
        'И' => 'i',   'Й' => 'y',   'К' => 'k',
        'Л' => 'l',   'М' => 'm',   'Н' => 'n',
        'О' => 'o',   'П' => 'p',   'Р' => 'r',
        'С' => 's',   'Т' => 't',   'У' => 'u',
        'Ф' => 'f',   'Х' => 'h',   'Ц' => 'c',
        'Ч' => 'ch',  'Ш' => 'sh',  'Щ' => 'sch',
        'Ь' => '\'',  'Ы' => 'y',   'Ъ' => '\'',
        'Э' => 'e',   'Ю' => 'yu',  'Я' => 'ya',
    );
    return strtr($str, $converter);
}

//@description Детранслитерация строки
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

//@description Перевод числа в слово
function num2word($num, $words)
{
    $num = $num % 100;
    if ($num > 19) {
        $num = $num % 10;
    }
    switch ($num) {
        case 1: {
            return($words[0]);
        }
        case 2: case 3: case 4: {
        return($words[1]);
    }
        default: {
            return($words[2]);
        }
    }
}