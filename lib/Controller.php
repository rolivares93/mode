<?php namespace Lib;

use Lib\Database;
use Lib\Session;
use Lib\View;

class Controller
{
	public function database($name = false, $user = false, $pass = false, $type = false, $host = false, $char = false)
	{
		return new Database($name, $user, $pass, $type, $host, $char);
	}

	public function session($sessionKey = false, $sessionName = false, $sessionCookie = false)
	{
		return new Session($sessionKey, $sessionName, $sessionCookie);
	}

	public function view($view, $data = [])
	{
		return new View($view, $data);
	}
}
