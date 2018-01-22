<?php
namespace Core\Classes;

class Router
{
	private $routes;

	public function __construct()
	{
		
	}

	public function run()
	{
		echo 'it`s working!\n';
		dump($_SERVER);
		dump($_SERVER['QUERY_STRING']);
		dump($_SERVER['QUERY_STRING']);
	}

	public function getURL()
	{

	}
}