<?php
namespace Core\Classes;

interface iCUser
{
	public function reg($userLogin, $userPassword, $userEmail); //Регистрация
	
	public function auth($userLogin, $userPassword, $userEmail); //Авторизация
	public function authById($userId); //Авторизация по ID юзера
	public function authByHash($hash); //Авторизация по MD5/SHA хэшу
	
	public function rememberUser($userLogin, $userPassword, $userEmail); //Запомнить юзера (сохранить хэш логин+пароль в куках и авторизоаывать по нему)
	public function restorePassword($userLogin, $userEmail); //Восстановление пароля по логину/email
	
	public function addUserToGroup($userId); //Добавление пользователя в группу пользователей
	public static function createUserGroup(); //Создать группу для пользователей (Подумать над параметрами: Права юзеров и т.д.)
	public function getUserGroup(); //Получить группы пользователя
	
	public function isAdmin(); //Является ли пользователь администратором
}

class CUser implements iCuser()
{
	
}