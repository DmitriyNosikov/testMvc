<?php
	namespace Core\Classes;
	
	/* 
	* Протестировать методы: del(), update()
	* ОБновить метод insert() - сделать проверку наличия добавляемой записи в БД
	* для обеспечения уникальности добавляемой информации.
	*
	*/
	
	function dump($str)
	{
		echo "<pre>";
		print_r($str);
		echo "</pre>";
	};
	
	class SQL
	{
		private $id = "";
		public $enable_error = true; //Включить/выключить вывод ошибок
		
		public function __construct($config_path)
		{
			if($config_path && require_once($config_path))
			{				
				$id = @mysqli_connect($DBHost, $DBLogin, $DBPassword, $DBName);
				if($id) $this->id = $id;
				else die("Невозможно подключиться к базе данных. Проверьте настройки подключения $config_path");
			}
			else $this->showError('<b>Ошибка:</b> Классу SQL необходимо передать путь к файлу с конфигурациями подключения:
			$DBHost = "Имя хоста",<br>
			$DBLogin = "Логин пользователя БД",<br>
			$DBPassword = "Пароль пользователя БД",<br>
			$DBName = "Имя БД"<br>');
		}
		
		/* ----- Методы для работы непосредственно с запросами ----- */
		
		/* 
		* Выволнение произвольного запроса
		* var @query - строка запроса
		*/
		public function query($query)
		{
			$query = $this->sanitizeString($query);
			$query = $this->sanitizeSql($query);
			
			$res = mysqli_query($this->id, $query);
			
			if($res) return mysqli_fetch_assoc($res);
			$this->showSQLerror();	
		}
		
		public function select($tableName, $col=false, $val=false)
		{
			if($col && $val) $query = "SELECT * FROM $tableName WHERE `$col`='$val';";
			else $query = "SELECT * FROM $tableName;";
			
			$val = $this->sanitizeString($val);
			$query = $this->sanitizeSql($query);

			$res = mysqli_query($this->id, $query);
			
			if($res) return mysqli_fetch_assoc($res);
			$this->showSQLerror();	
		}
		
		public function selectAll($tableName, $col=false, $val=false)
		{
			if($col && $val) $query = "SELECT * FROM $tableName WHERE `$col`='$val';";
			else $query =  "SELECT * FROM $tableName;";
			
			$query = $this->sanitizeSql($query);
			
			$resQuery = mysqli_query($this->id, $query);
			$res = array();
						
			if($resQuery)
			{
				while(true)
				{
					if(!$row = mysqli_fetch_assoc($resQuery)) break;
					$res[] = $row;
				}
				return $res;
			}
			
			$this->showSQLerror();
		}
		
		public function insert($tableName, $valuesArr)
		{
			$fields = "";
			$values = "";
			$columns = $this->getCols($tableName);
			$charset = $this->getCharset();
			
			if(is_array($valuesArr))
			{
				$i = 0;
				foreach($valuesArr as $key=>$val)
				{
					if(in_array($key, $columns))
					{
						$val = $this->sanitizeString($val);
						$fields .= "`".$key."`";
						$values .= "'".$val."'";
						$i++;
				
						if($i < count($valuesArr))
						{
							$fields .= ", ";
							$values .= ", ";	
						}
					}
				}
 
				if(mb_strlen($fields, $charset) == 0) return false;
				if(mb_strlen($values, $charset) == 0) return false;

				$query = "INSERT INTO $tableName ($fields) VALUES ($values);";
				$query = $this->sanitizeSql($query);
				
				if(mysqli_query($this->id, $query)) return mysqli_insert_id($this->id);
				else $this->showSQLerror();
			}
			else $this->showError('Второй параметр ($valuesArr) для метода insert() должен быть ассоциативным массивом.');
		}
		
		public function update($tableName, $valuesArr, $col, $operator, $where)
		{
			$values = "";
			$columns = $this->getCols($tableName);
			$operatorsArr = array(">", "<", "=", ">=", "<=", "!=");
			$charset = $this->getCharset();	
			
			if(is_array($valuesArr))
			{
				$i = 0;
				foreach($valuesArr as $key=>$val)
				{
					if(in_array($key, $columns))
					{
						$val = mysqli_real_escape_string($this->id, $val);
						$values .= "`".$key."`='".$val."'";
						$i++;
				
						if($i < count($valuesArr)) $values .= ", ";	
					}
				}

				if(mb_strlen($values, $charset) == 0) return false;

				if($operator)
				{
					if(!in_array($operator, $operatorsArr));
					else $operator == "=";
				}
				else $operator == "=";
				
				if($col && $where) $query= "UPDATE $tableName SET $values WHERE `$col`".$operator."'$where';";
				else $query = "UPDATE $tableName SET $values;";
			
				$query = $this->sanitizeSql($query);
			
				$res = mysqli_query($this->id, $query);
				
				if($res) return $res;
				else $this->showSQLerror();
			}
			else $this->showError('Второй параметр ($valuesArr) для метода update() должен быть ассоциативным массивом.');	
		}
		
		public function del($tableName, $col, $operator, $where)
		{
			$operatorsArr = array(">", "<", "=", ">=", "<=", "!=");
			
			if($operator)
			{
				if(!in_array($operator, $operatorsArr));
				else $operator == "=";
			}
			else $operator == "=";
			
			if($col && $where) $query= "DELETE FROM $tableName WHERE `$col`".$operator."'$where';";
			else $this->showError("Ошибка: Методу del() необходимо передать условие удаления вида '$col'['>', '<', '=', '>=', '<=', '!=']'$where';");
			
			$query = $this->sanitizeSql($query);
			
			$res = mysqli_query($this->id, $query);
			
			if($res) return $res;
			else $this->showSQLerror();
		}
		
		/* ----- Технические методы ----- */
		
		// Метод для полученя количества строк, затронутых последним запросом к БД
		public function getRowsCount()
		{
			return mysqli_affected_rows($this->id);
		}
		
		//Получение столбцов таблицы
		public function getCols($tableName)
		{
			$query = "SHOW COLUMNS FROM $tableName;";
			$query = $this->sanitizeSql($query);
			
			$res = mysqli_query($this->id, $query);
			$fields = array();
			
			if($res)
			{	
				while(true)
				{
					if(!$row = mysqli_fetch_assoc($res)) break;
					$fields[] = $row['Field'];
				}
				
				return $fields;
			}	
			else $this->showSQLerror();
		}
		
		public function selectDB($DBName)
		{
			mysqli_select_db($this->id, $DBName);
		}
		
		public function setCharset($charset)
		{
			if($charset) mysqli_set_charset($this->id, $charset);
			else mysqli_set_charset($this->id, "utf8");
		}
		
		public function getCharset()
		{
			return mysqli_character_set_name($this->id);
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
		
		public function showSQLerror()
		{
			echo "Ошибка работы с базой данных (".mysqli_errno($this->id)."): ".mysqli_error($this->id)."<br>";
			return false;
		}
		
		public function close()
		{
			mysqli_close($this->id);
		}
			
		//Очистка любых нечисловых (т.е. строковых) данных перед записью в БД
		public function sanitizeString($str)
		{
			if(gettype($str) == 'string') return mysqli_real_escape_string($this->id, $str);
			else return $str;
		}
			
		/* 	
		* Комплексная очистка данных от SQL и XSS кода,
		* а также различного рода html тэгов
		* Функции очистки пользовательского ввода 
		*/
		public function sanitizeSql($str, $strip_tags=false)
		{
			if (get_magic_quotes_gpc()) $str = stripslashes($str);
			
			$str= htmlspecialchars($str);
			
			//Если необходимо очистить ввод от html тэгов, передать
			//аргументу $strip_tags функции параметр 1 или true
			if ($strip_tags) $str = strip_tags($str);
			
			return $str;
		}
	}
?>