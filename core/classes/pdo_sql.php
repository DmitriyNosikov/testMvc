<?php
namespace Core\Classes;
//Попробуем написать свою библиотеку для использования PDO
interface standartSql
{
	/*
	* @description - Выполнение простого произвольного неподготовленного запроса
	* @param {string} $queryString - Строка запроса
	*/
	public function simpleQuery($queryString);

	/*
	* @description - Выполнение произвольного подготовленного запроса
	* @param {string} $queryString - Строка запроса
	* @param {array} $queryParams - Массив параметров для подготовленного запроса.
	*/
	public function prepareQuery($queryString, $queryParams);

	/*
	* @description - Метод возвращает 1 запись из таблицы $tableName по условию $conditionArr
	* @param {string} $tableName - Название таблицы, из которой проихводится выборка
	* @param {array} $conditionArr - Ассоциативный массив вида название_столбца => значение,
	* каждый элемент такого массива в итоге превращается в условие WHERE название_столбца => значение AND название_столбца_N => значение_N
	*/
	public function selectOne($tablename, $conditionArr);

	public function selectAll();

	/*
	* @description - Метод вставляет запись в таблицу $tablename
	* @param {string} $tableName - Название таблицы, в котороую производится запись данных
	* @param {array} $valuesArr - Ассоциативный массив вида название_столбца => значение,
	* каждый элемент такого массива в итоге превращается в строку вида:
	* INSERT INTO $tablename (ключи_массива_$valuesArr) VALUES (значения_массива_$valuesArr)
	*/
	public function insert($tablename, $valuesArr);

	public function update();
	public function delete();

	/*
	* @description - Метод возвращает количество строк, затронутых запросами DELETE, UPDATE или INSERT объекта, переданного в $stmt
	* @param {obj} $stmt - Объект запроса PDOstmt
	* @param {array} $conditionArr - Ассоциативный массив вида название_столбца => значение,
	* каждый элемент такого массива в итоге превращается в условие WHERE название_столбца => значение AND название_столбца_N => значение_N
	*/
	public function affectedRows($stmt);

	public function selectedRowsCount();

	public function closeConnection();
}

class PDO_SQL implements standartSql 
{
	public $pdo = null;

	public function __construct($dbHost, $dbName, $dbCharset, $userName, $userPass)
	{
		$dsn = 'mysql:host='.$dbHost.';dbname='.$dbName.';charset='.$dbCharset;
		$dsnOpt = array(
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	        PDO::ATTR_EMULATE_PREPARES => false,
		);

		$this->pdo = new PDO($dsn, $userName, $userPass, $dsnOpt);
	}

	public function simpleQuery($queryString)
	{
		$stmt = $this->pdo->query($queryString);
		
		return $stmt->fetchAll();
	}

	public function prepareQuery($queryString, $queryParams)
	{
		$stmt = $this->pdo->prepare($queryString);
		$affectedRows = $stmt->execute($queryParams);
		$result = $stmt->fetchAll();

		if(!empty($result)) return $result;
		else return $affectedRows;
	}

	public function selectOne($tablename, $conditionArr)
	{

	}

	public function selectAll()
	{

	}

	public function insert($tablename, $valuesArr)
	{
		if(!is_array($valuesArr)) return false;

		$fields = ''; //Массив полей для подготовленного запроса (столбцы в таблице $tablename)
		$fieldsValues = array(); //Массив со значениями полей для подготовленного запроса
		$preparedValues = ''; //псевдопеременные ? для подготовленного запроса
		$counter = 0;

		foreach($valuesArr as $key => $val)
		{
			$fields .= "`".$key."`";
			$fieldsValues[] = $val;
			$preparedValues .= "?";

			$counter++;

			if($counter < count($valuesArr))
			{
				$fields .= ', ';
				$preparedValues .= ', ';
			}
		}

		if(mb_strlen($fields, 'utf8') == 0) return false;
		if(count($fieldsValues) == 0) return false;

		$queryStr = "INSERT INTO $tablename ($fields) VALUES ($preparedValues);";

		print_r($queryStr);

		return $this->prepareQuery($queryStr, $fieldsValues);
	}

	public function update()
	{
	}

	public function delete()
	{
	}

	public function affectedRows($stmt)
	{
		return $stmt->rowCount();
	}

	public function selectedRowsCount()
	{
	}

	public function closeConnection()
	{
		$pdo = null;
	}
}