<?php namespace Lib;

use App\Controllers;

class Router
{
	private $uri = '';
	private $routes = [];
	private $replacements = [];

	public function __construct()
	{
		$this->replacements = [
			'[id]' => '([0-9]+)',
			'[alnum]' => '([a-zA-Z0-9]+)',
			'[string]' => '([a-zA-Z0-9-_]+)'
		];

		$this->uri = isset($_GET['uri']) ? rtrim($_GET['uri'], '/').'/' : '/';
	}

	public function request($route, $callback)
	{
		$route = rtrim($route, '/').'/';
		$replaced = str_replace(array_keys($this->replacements), array_values($this->replacements), $route);
		$pattern = '#^'.str_replace('/', '\/', $replaced).'$#';
		$this->routes[$pattern] = $callback;
	}

	public function response()
	{
		foreach ($this->routes as $pattern => $callback) {
			if (preg_match($pattern, $this->uri, $params)) {
				array_shift($params);

				return call_user_func_array($callback, array_values($params));
			}
		}

		$error = new Controllers\ErrorController();
		$error->error404();
	}

	public function setReplacements($placeholder, $replacement)
	{
		$this->replacements[$placeholder] = $replacement;
	}
}
