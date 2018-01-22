<?php
namespace Core\Classes;
//Попробуем написать свою библиотеку для использования PDO
interface standartSql
{
	/**
	 * Выполнение простого произвольного неподготовленного запроса
	 * @param [string] $queryString Строка запроса
	 * @return [type]              [description]
	 */
	public function simpleQuery($queryString);

	/**
	 * Выполнение произвольного подготовленного запроса
	 * @param [string] $queryString Строка запроса
	 * @param [array] $queryParams Массив параметров для подготовленного запроса.
	 * @return [type]              [description]
	 */
	public function prepareQuery($queryString, $queryParams);

	/**
	 * Метод возвращает 1 запись из таблицы $tableName по условию $conditionArr
	 * @param [string] $tablename Название таблицы, из которой проихводится выборка
	 * @param [array] $conditionArr Ассоциативный массив вида название_столбца => значение,
	 * каждый элемент такого массива в итоге превращается в условие WHERE название_столбца => значение AND название_столбца_N => значение_N
	 * @return [type] [description]
	 */
	public function selectOne($tablename, $conditionArr);

	public function selectAll($tableName, $conditionArr);

	/**
	 * Метод вставляет запись в таблицу $tablename
	 * @param [string] $tablename Название таблицы, в котороую производится запись данных
	 * @param [array] $valuesArr Ассоциативный массив вида название_столбца => значение,
	 * каждый элемент такого массива в итоге превращается в строку вида:
	 * INSERT INTO $tablename (ключи_массива_$valuesArr) VALUES (значения_массива_$valuesArr)
	 * @return [type] [description]
	 */
	public function insert($tablename, $valuesArr);

	public function update();
	public function delete();

	/**
	 * Метод возвращает количество строк, затронутых запросами DELETE, UPDATE или INSERT объекта, переданного в $stmt
	 * @param [obj] $stmt Объект запроса PDOstmt
	 * @return [array] Ассоциативный массив вида название_столбца => значение,
	 * каждый элемент такого массива в итоге превращается в условие WHERE название_столбца => значение AND название_столбца_N => значение_N
	 * @return [type] [description]
	 */
	public function affectedRows($stmt);

	public function selectedRowsCount();

	public function closeConnection();
}



class PDOSql implements standartSql 
{
	public $pdo = null;

	public function __construct($dbHost, $dbName, $dbCharset, $userName, $userPass)
	{
		$dsn = 'mysql:host='.$dbHost.';dbname='.$dbName.';charset='.$dbCharset;
		$dsnOpt = array(
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, //Вывод ошибок (Только исключения)
	        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, //Методы вывода результата запроса по-умолчанию
	        \PDO::ATTR_EMULATE_PREPARES => false, //Эмулирование подготовленных запросов
		);

		$this->pdo = new \PDO($dsn, $userName, $userPass, $dsnOpt);
	}

	/**
	 * [simpleQuery description]
	 * @param [string] $queryString Строка запроса в стандартном SQL формате
	 * @return [array] $return Возвращает результат выполнения запроса или пустой массив
	 */
	public function simpleQuery($queryString)
	{
		$stmt = $this->pdo->query($queryString);

		return $stmt->fetchAll();
	}

	public function prepareQuery($queryString, $queryParams)
	{
		$stmt = $this->pdo->prepare($queryString);
		$affectedRows = $stmt->execute($queryParams);

		$result = array();

		//Реализовано через fetch(), т.к. при больших выборках, fetchAll() без фильтрации может сильно нагружать сервер 
		//(http://php.net/manual/ru/pdostatement.fetchall.php)
		while($row= $stmt->fetch())
		{
			$result[] = $row;
		}

		if(!empty($result)) return $result;
		else return $affectedRows;
	}

	/**
	 * Метод для выборки 1 записи из таблицы БД
	 * @param [string] $tableName Название таблицы, из которой необходимо выбрать элемент
	 * @param [array] $conditionArr Массив условий выборки вида:
	 * array('id' => 9, 'name' => 'test'), где ключи - названия столбцов, в значения - соджержимое строк
	 * @return [array] $result Возвращает итоговую выборку из 1 элемента или пустой массив
	 */
	public function selectOne($tableName, $conditionArr)
	{
		//Преобразуем массив $conditionArr, собирая из него строку с плейсхолдерами и массив с их значениями
		$selectPlaceholder = $this->getSelectPlaceholder($conditionArr);

		if(!$selectPlaceholder) return false; //Если не удалось подготовить плейсхолдеры для запроса

		$queryStr = "SELECT * FROM $tableName WHERE ".$selectPlaceholder['SPH_STR']." LIMIT 1;"; //Итоговая строка запроса с плейсхолдерами

		//Передаем подготовленную строку, а также значения плейсхолдеров методу для реализации запроса
		return $this->prepareQuery($queryStr, $selectPlaceholder['SPH_VAL']);
	}

	/**
	 * Метод для выборки нескольких записей из таблицы БД (Тоже самое, что и selectOne, только для множественной выборки)
	 * @param [string] $tableName Название таблицы, из которой необходимо выбрать элемент
	 * @param [array] $conditionArr Массив условий выборки вида:
	 * array('id' => 9, 'name' => 'test'), где ключи - названия столбцов, в значения - соджержимое строк
	 * @return [array] $result Возвращает итоговую выборку из 1 элемента или пустой массив
	 */
	public function selectAll($tableName, $conditionArr)
	{
		//Преобразуем массив $conditionArr, собирая из него строку с плейсхолдерами и массив с их значениями
		$selectPlaceholder = $this->getSelectPlaceholder($conditionArr);

		if(!$selectPlaceholder) return false; //Если не удалось подготовить плейсхолдеры для запроса

		$queryStr = "SELECT * FROM $tableName WHERE ".$selectPlaceholder['SPH_STR'].";"; //Итоговая строка запроса с плейсхолдерами

		//Передаем подготовленную строку, а также значения плейсхолдеров методу для реализации запроса
		return $this->prepareQuery($queryStr, $selectPlaceholder['SPH_VAL']);
	}


	/**
	 * Вспомогательный метод для подготовки плэйсхолдеров для запросов типа Select
	 * @param [array] $conditionArr Массив условий выборки вида:
	 * array('id' => 9, 'name' => 'test'), где ключи - названия столбцов, в значения - соджержимое строк
	 * @return [array] $result Возвращает массив с двумя элементами: SPH_STR - Строка с плейсхолдерами, SPH_VAL - Массив со значениями плейсхолдеров
	 */
	protected function getSelectPlaceholder($conditionArr)
	{
		if(!is_array($conditionArr)) return false;

		$placeholderArr = array(); //Массив с плейсхолдерами
		$placeholderValuesArr = array(); //Массив со значениями плэйсхолдеров
		
		foreach($conditionArr as $condKey => $condVal)
		{
			$placeholderArr[] = "`".$condKey."` = ?";
			$placeholderValuesArr[] = $condVal;
		}

		$placeholderStr = implode(' AND ', $placeholderArr); //Собираем условие из массива в строку с плэйсхолдерами вместо значений. Пока только по условию AND.
		$result = array('SPH_STR' => $placeholderStr, 'SPH_VAL' => $placeholderValuesArr); //Итоговый массив, содержащий строку с плейсзолдерами и массив с их значениями

		return $result;
	}

	/**
	 * Метод для вставки новой записи в таблицу
	 * @param [string] $tableName Название таблицы, из которой необходимо выбрать элемент
	 * @param [array] $valuesArr Массив значений, которые необходимо вставить в таблиц.
	 * Количество значений должно соответствовать  количеству столбцов в     таблице
	 * @return [type] [description]
	 */
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

	public function getDrivers()
	{
		return \PDO::getAvailableDrivers();
	}

	public function closeConnection()
	{
		$pdo = null;
	}
}