<?php

class Security
{
	//Метод для добавления нового IP адреса в список заблокированных
	public static function blockIP($IPAddress, $IPListID)
	{
		self::updateIPBlockList($IPAddress, $IPListID, 'add');
	}
	
	//Метод для удаления IP адреса из списка заблокированных
	public static function unblockIP($IPAddress, $IPListID)
	{
		self::updateIPBlockList($IPAddress, $IPListID, 'remove');
	}
	
	//Метод для обновления списков IP адресов
	public static function updateIPBlockList($IPAddress, $IPListID, $IPAction)
	{
		if(empty($IPAddress) || (empty($IPListID) && (!is_string($IPListID) && !is_int($IPListID))) || !is_string($IPAction)) return false;
		
		$IPList = self::getBlockedIPList($IPListID);
		
		switch($IPAction)
		{
			case 'remove':
			{
				$IPKey = array_search($IPAddress, $IPList['BLOCKED_IP_SINGLE']);
				
				if(is_int($IPKey))
				{
					unset($IPList['BLOCKED_IP_SINGLE'][$IPKey]);
					sort($IPList['BLOCKED_IP_SINGLE']);
				}
				else return true;

				break;
			}
			
			case 'add':
			{
				if(!in_array($IPAddress, $IPList['BLOCKED_IP_SINGLE'])) $IPList['BLOCKED_IP_SINGLE'][] = $IPAddress;
				else return true;
				
				break;
			}
		}
		
		CIBlockElement::SetPropertyValuesEx($IPListID, 10, array('BLOCKED_ID_ADDRESS' => $IPList['BLOCKED_IP_SINGLE']));
		return true;
	}
		
	//Метод для проверки IP на наличие в списке заблокированных
	public static function isBlockedIP($IPAddress)
	{
		if(empty($IPAddress) || !is_string($IPAddress)) return false;
		
		$blockedIPList = self::getBlockedIPList();
		$isBlockedIP = false;
		
		if(!empty($blockedIPList))
		{
			//Если IP адрес есть в списке блокируемых IP и отсутствует в списке исключений
			if(in_array($IPAddress, $blockedIPList['BLOCKED_IP_SINGLE']) && !in_array($IPAddress, $blockedIPList['BLOCKED_IP_EXCEPTION']))
			{
				$isBlockedIP = true;
			}
			
			if($isBlockedIP) return $isBlockedIP; //Если IP уже найден в списке заблокированных
			
			foreach($blockedIPList['BLOCKED_IP_GROUPS'] as $IPGroup)
			{
				$IPAddress = self::ip2number($IPAddress);
				$IPGroupExploded = explode('-', $IPGroup);
				$IP_1 = self::ip2number(trim($IPGroupExploded[0]));
				$IP_2 = self::ip2number(trim($IPGroupExploded[1]));
				
				if($IP_2 <= 0) $IP_2 = $IP_1;
				
				if($IPAddress >= $IP_1 && $IPAddress <= $IP_2)
				{
					$isBlockedIP = true;
					break;
				}
			}
		}
		
		return $isBlockedIP;
	}
	
	/*
		@description Метод для получения IP адреса
		@param $checkProxy - Если задан, метод пытается получить реальный IP адрес пользователя, использующего прокси
	*/
	public static function getUserIP($checkProxy = false)
	{
		if($checkProxy)
		{
			if(!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) return $_SERVER['HTTP_X_FORWARDED_FOR'];
			elseif(!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) return $_SERVER['HTTP_CLIENT_IP'];
		}
		
		return $_SERVER['REMOTE_ADDR'];
	}
	
	//Метод для получения списка заблокированных IP адресов и их групп, а также списка исключений
	private static function getBlockedIPList($IPListID = false)
	{
		if(CModule::IncludeModule('iblock'))
		{
			$blockedIPFilter = array('IBLOCK_ID' => 10, 'ACTIVE' => 'Y');
			
			//Если передан ID списка
			if(!empty($IPListID) && (is_string($IPListID) || is_int($IPListID))) $blockedIPFilter['ID'] = $IPListID;
			
			$blockedIPObj = CIBlockElement::GetList(
				array(),
				$blockedIPFilter, 
				false,
				false, 
				array(
					'ID',
					'NAME',
					'DATE_ACTIVE_FROM',
					'DATE_ACTIVE_TO',
					'PROPERTY_BLOCKED_ID_ADDRESS',
					'PROPERTY_BLOCKED_PATH',
					'PROPERTY_BLOCKED_IP_EXCEPTION',
					'PROPERTY_BLOCKED_PATH_EXCEPTION'
				)
			);
			
			$IPGroups = array(); //Если в поле IP указан диапазон, то диапазон попадает в этот массив
			$IPSingle = array(); //В жанный массив попадают все одиночные IP адреса
			$IPException = array(); //Исключения из IP адресов
			
			while($blockedIPList = $blockedIPObj->Fetch())
			{
				//Распределяем IP адреса по группам и одиночным IP
				foreach($blockedIPList['PROPERTY_BLOCKED_ID_ADDRESS_VALUE'] as $IP)
				{
					if(preg_match('#\-#', $IP)) $IPGroups[] = $IP;
					else $IPSingle[] = $IP;
				}
				
				//Также собираем массив исключений из IP адресов
				if(!empty($blockedIPList['PROPERTY_BLOCKED_IP_EXCEPTION_VALUE'])) $IPException = array_merge($IPException, $blockedIPList['PROPERTY_BLOCKED_IP_EXCEPTION_VALUE']);
			}
			
			if(empty($IPGroups) && empty($IPSingle)) return array();
			
			$IPListResult = array(
				'BLOCKED_IP_GROUPS' => $IPGroups,
				'BLOCKED_IP_SINGLE' => $IPSingle,
				'BLOCKED_IP_EXCEPTION' => $IPException
			);
			
			return $IPListResult;
		}
	}
	
	//Перевод IP из общепринятого формата в число
	public static function ip2number($ip)
	{
		$ip = trim($ip);
		if(strlen($ip) > 0)
			$res = doubleval(sprintf("%u", ip2long($ip)));
		else
			$res = 0;
		return $res;
	}
}