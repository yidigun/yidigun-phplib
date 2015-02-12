<?php

namespace Yidigun;

use Yidigun\Util\ArrayParams;

class Cookie extends ArrayParams {

	public $path = "/";

	public $domain = null;

	public $secure = false;

	public $httponly = false;

	public function __construct() {
		parent::__construct($_COOKIE);
		$server = new Server();
		$this->domain = $server->HTTP_HOST;
	}

	public function &ref($name) {
		if ($this->has($name))
			return $this->v[$name];
		else
			throw new \Exception("unknown cookie: {$name}");
	}

	public function set($name, $value, $expire = 0) {
		$this->setCookie($name, $value, $expire);
	}

	public function setRef($name, &$ref) {
		throw new \Exception("Cookie does not support reference");
	}

	public function setIfEmpty($name, $value, $expire = 0) {
		if ($this->isEmpty($name)) {
			$this->setCookie($name, $value, $expire);
		}
	}

	public function setRefIfEmpty($name, &$ref) {
		throw new \Exception("Cookie does not support reference");
	}

	public function setCookie($name, $value, $expire = 0, $path = null, $domain = null, $secure = null, $httponly = null) {
		if (!$path)
			$path = $this->path;
		if (!$domain)
			$domain = $this->domain;
		if ($secure === null)
			$secure = $this->secure;
		if ($httponly === null)
			$httponly = $this->httponly;

		$this->call_setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
		$this->v[$name] = $value;
	}

	protected function call_setcookie($name, $value, $expire, $path, $domain, $secure, $httponly) {
		setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}
}
