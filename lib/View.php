<?php namespace Lib;

use App\Controllers;

class View
{
	private $file;
	private $data;

	public function __construct($view, $data = [])
	{
		$view = mb_strtolower($view);
		$this->file = BASE_PATH.'app/views/'.$view.'.php';
		$this->data = $data;
		$this->render();
	}

	private function render()
	{
		if (!file_exists($this->file)) {
			$error = new Controllers\ErrorController();
			$error->fileDoesNotExist($this->file);

			return false;
		}

		extract($this->data);

		require_once $this->file;
	}
}
