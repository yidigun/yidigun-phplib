<?php
namespace Yidigun\DB;


class ConnectionManager {

	private static $defaultInstance = null;

	public static function getDefault() {
		if (self::$defaultInstance == null) {
			self::$defaultInstance = new ConnectionManager();
		}
		return self::$defaultInstance;
	}

	public static function setDefaultAlias($alias, $dsnOrURL) {
		$connMan = self::getDefault();
		$connMan->setAlias($alias, $dsnOrURL);
	}

	public static function setDefaultAliases($aliases) {
		$connMan = self::getDefault();
		$connMan->setAliases($aliases);
	}

	public static function getDefaultDataSource($dsnOrAlias) {
		$connMan = self::getDefault();
		return $connMan->getDataSource($dsnOrAlias);
	}

	public static function getDefaultConnection($dsnOrAlias) {
		$connMan = self::getDefault();
		return $connMan->getConnection($dsnOrAlias);
	}

	private $conns = array();
	private $dsns = array();
	private $aliases = array();

	public function __construct() {
		/* no nothing */
	}
	
	public function __desctruct() {
		$this->closeAll();
	}

	public function getDataSource($dsnOrAlias) {
		if ($this->aliases[$dsnOrAlias])
			$ds = $this->aliases[$dsnOrAlias];
		else
			$ds = DataSource::parse($dsnOrAlias);

		return $ds;
	}

	public function getConnection($dsnOrAlias) {

		$ds = $this->getDataSource($dsnOrAlias);
		$url = $ds->toURL();

		if (!$this->conns[$url]) {
			$conn = $ds->connect();
			$this->conns[$url] = $conn;
		}
		return $this->conns[$url];
	}

	public function setAlias($alias, $dsnOrURL) {
		$this->aliases[$alias] = DataSource::parse($dsnOrURL);
	}

	public function setAliases($aliases) {
		foreach ($aliases as $alias => $dsnOrURL) {
			$this->aliases[$alias] = DataSource::parse($dsnOrURL);
		}
	}

	public function close($dsnOrAlias) {
		$ds = $this->getDataSource($dsnOrAlias);
		$url = $ds->toURL();
		
		if ($this->conns[$url]) {
			$this->conns[$url]->rollBack();
			unset($this->conns[$url]);
		}
	}

	public function closeAll() {
		$ds = $this->getDataSource($dsnOrAlias);
		$url = $ds->toURL();

		foreach ($this->conns[$url] as $conn) {
			$conn->rollBack();
			unset($this->conns[$url]);
		}
	}
}
