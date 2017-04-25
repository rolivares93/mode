<?php namespace Lib;

use SessionHandler;

class Session extends SessionHandler
{
	protected $sessionKey;
	protected $sessionName;
	protected $sessionCookie = [];

	public function __construct($sessionKey = false, $sessionName = false, $sessionCookie = false)
	{
		$this->sessionKey = ($sessionKey) ? $sessionKey : bin2hex(date('ymd'));
		$this->sessionName = ($sessionName) ? $sessionName : sha1($this->sessionKey);
		$this->sessionCookie += [
			'lifetime' => 60*60*24,
			'path' => BASE_URL,
			'domain' => ini_get('session.cookie_domain'),
			'secure' => isset($_SERVER['HTTPS']),
			'httponly' => true
		];
		$this->setup();
	}

	private function setup()
	{
		ini_set('session.use_cookies', true);
		ini_set('session.use_only_cookies', true);
		ini_set('session.cookie_httponly', true);
		ini_set('session.cookie_secure', true);
		session_save_path(BASE_PATH.SESSIONS);
		session_name($this->sessionName);
		session_set_cookie_params(
			$this->sessionCookie['lifetime'],
			$this->sessionCookie['path'],
			$this->sessionCookie['domain'],
			$this->sessionCookie['secure'],
			$this->sessionCookie['httponly']
		);
	}

	public function start()
	{
		if (session_id() === '') {
			if (session_start()) {
				return (mt_rand(0, 4) === 0) ? session_regenerate_id(true) : true;
			}
		}

		return false;
	}

	public function finish()
	{
		if (session_id() === '') {
			return false;
		}

		$_SESSION = [];

		setcookie(
			$this->sessionName,
			'',
			time() - 42000,
			$this->sessionCookie['path'],
			$this->sessionCookie['domain'],
			$this->sessionCookie['secure'],
			$this->sessionCookie['httponly']
		);

		return session_destroy();
	}

	public function get($name)
	{
		$parsed = explode('.', $name);
		$result = $_SESSION;

		while ($parsed) {
			$next = array_shift($parsed);

			if (isset($result[$next])) {
				$result = $result[$next];
			} else {
				return null;
			}
		}

		$result = $this->encryptDecrypt('decrypt', $result);

		return $result;
	}

	public function set($name, $value)
	{
		$parsed = explode('.', $name);
		$session =& $_SESSION;

		while (count($parsed) > 1) {
			$next = array_shift($parsed);

			if (!isset($session[$next]) || !is_array($session[$next])) {
				$session[$next] = [];
			}

			$session =& $session[$next];
		}

		$value = $this->encryptDecrypt('encrypt', $value);

		$session[array_shift($parsed)] = $value;
	}

	private function encryptDecrypt($action, $string)
	{
		$output = false;
		$encrypt_method = 'AES-256-CBC';
		$secret_key = $this->sessionKey;
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_key), 0, 16);

		if ($action == 'encrypt') {
			$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
			$output = base64_encode($output);
		} else if ($action == 'decrypt') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}

	public function on()
	{
		return (isset($_SESSION['on']) && $this->get('on'));
	}
}

