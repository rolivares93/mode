<?php namespace Lib;

use PDO;

class Database extends PDO
{
	private $name = '';
	private $user = '';
	private $pass = '';
	private $type = '';
	private $host = '';
	private $char = '';
	private $conn = null;

	public function __construct($name = false, $user = false, $pass = false, $type = false, $host = false, $char = false)
	{
		$this->type = ($type) ? $type : DB_TYPE;
		$this->host = ($host) ? $host : DB_HOST;
		$this->name = ($name) ? $name : DB_NAME;
		$this->user = ($user) ? $user : DB_USER;
		$this->pass = ($pass) ? $pass : DB_PASS;
		$this->char = ($char) ? $char : DB_CHAR;

		$this->connect();
	}

	private function connect()
	{
		try {
			$this->conn = new PDO($this->type.':host='.$this->host.';dbname='.$this->name.';charset='.$this->char, $this->user, $this->pass);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch(PDOException $e) {
			$this->conn = $e->getMessage();
		}
	}

	public function create($array)
	{
		$sql = $array['sql'];
		$stmt = $this->conn->prepare($sql);
		$response = ($stmt->execute($array['vals'])) ? true : false;
		$this->conn = null;

		return $response;
	}

	public function read($array)
	{
		$sql = $array['sql'];
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($array['vals']);
		$response = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->conn = null;

		return $response;
	}

	public function readAll($array)
	{
		$sql = $array['sql'];
		$stmt = $this->conn->prepare($sql);
		$stmt->execute($array['vals']);
		$response = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$this->conn = null;

		return $response;
	}

	public function update($array)
	{
		$sql = $array['sql'];
		$stmt = $this->conn->prepare($sql);
		$response = ($stmt->execute($array['vals'])) ? true : false;
		$this->conn = null;

		return $response;
	}

	public function delete($array)
	{
		$sql = $array['sql'];
		$stmt = $this->conn->prepare($sql);
		$response = ($stmt->execute($array['vals'])) ? true : false;
		$this->conn = null;

		return $response;
	}
}


