<?php

namespace Yidigun\DB;

use PDO;
use PDOException;

abstract class DataSource {

	protected $scheme;
	protected $dsInfo;
	
	/*
	 * TODO implement options management
	 */
	protected $options = array(
		PDO::ATTR_ERRMODE	=> PDO::ERRMODE_EXCEPTION,
	);

	public $username;
	public $password;

	protected function __construct($scheme) {
		$this->scheme = $scheme;
	}
	
	public function __toString() {
		return $this->URL();
	}
	
	public function equals($o) {
		if ($o != null && $o instanceof DataSource)
			return ($this->toURL() == $o->toURL());
		else
			return false;
	}

	/**
	 * singleton factory method
	 */
	public static function parse($dsnOrURL, $username = null, $password = null) {
		if ($dsnOrURL instanceof DataSource) {
			return $dsnOrURL;
		}
		elseif (preg_match('!^([a-z0-9]+):/!', $dsnOrURL, $parts)) {
			$urlInfo = parse_url($dsnOrURL);
			$ds = self::loadClass($urlInfo['scheme']);
			$ds->initFromURL($urlInfo);
		}
		else {
			$dsInfo = self::parseDSN($dsnOrURL);
			$ds = self::loadClass($dsInfo['scheme']);
			$ds->initFromDSN($dsInfo);
		}

		if ($username)
			$ds->username = $username;
		if ($password)
			$ds->password = $password;

		return $ds;
	}

	protected static function parseDSN($dsn) {
		if (!preg_match("!^([a-z0-9]+):(.*)$!", $dsn, $parts))
			return array();

		$dsInfo = array();
		$dsInfo['scheme'] = strtolower($parts[1]);
		foreach (preg_split('/\s*;\s*/', $parts[2]) as $row) {
			list($name, $value) = preg_split('/\s*=\s*/', $row, 2);
			$dsInfo[$name] = $value;
		}

		return $dsInfo;
	}

	protected static function loadClass($scheme) {
		$class = "\\" . __NAMESPACE__ . "\\DataSources\\" . strtolower($scheme);

		// try load contrete class
		//\__autoload($class);
		if (!class_exists($class))
			throw new \Exception("DataSource class is not found: {$class}");

		return new $class($scheme);
	}

	public function getType() {
		return $this->scheme;
	}

	protected function initFromDSN($dsInfo) {
		$this->dsInfo = $dsInfo;
		unset($this->dsInfo['scheme']);
	}

	public function toDSN() {
		$s = $this->scheme . ":";
		$len = count($this->dsInfo);
		$i = 0;
		foreach ($this->dsInfo as $name => $value) {
			$s .= $name . '=' . $value;
			if ($i < $len - 1)
				$s .= ';';
			$i++;
		}
		return $s;
	}

	protected abstract function initFromURL($urlInfo);
	public abstract function toURL();

	public function connect() {
		try {
			$pdo = new \PDO($this->toDSN(), $this->username, $this->password, $this->options);
			return $pdo;
		}
		catch (PDOException $e) {
			throw new PDOConnectionException($e, $this);
		}
	}

	public function newSQLBuilder() {
		return SQLBuilder::newSQLBuilder($this->scheme);
	}

}
