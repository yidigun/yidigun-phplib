<?php

namespace Yidigun\DB;

use \Exception;
use \PDO;
use \PDOException;
use \PDOStatement;

class DBManager {

	protected $debug = false;

	protected $connectionManager;
	protected $dsn;
	protected $pdo;

	public function __construct($dsnOrAlias, ConnectionManager $connectionManager = null) {
		$this->dsn = $dsnOrAlias;
		if ($connectionManager == null)
			$this->connectionManager = ConnectionManager::getDefault();
	}

	/*
	 * Connect to PDO data source
	 */

	public function connect() {
		if (!$this->pdo) {
			try {
				$this->pdo = $this->connectionManager->getConnection($this->dsn);
			}
			catch (PDOConnectionException $e) {
				$this->debug(__FUNCTION__, $this->dsn, $e);
				throw $e;
			}
		}
		return $this->pdo;
	}

	public function newSQLBuilder() {
		$ds = $this->connectionManager->getDataSource($this->dsn);
		$builder = $ds->newSQLBuilder();
		if ($this->isDebug())
			$builder->setDebug(true);
		return $builder;
	}

	/*
	 * low-level wrapper
	 */

	public function prepare($sql, array $options = array()) {

		if ($sql instanceof SQLBuilder)
			$sql = $sql->buildQuery();

		$pdo = $this->connect();

		try {
			$stmt = $pdo->prepare($sql, $options);
			return $stmt;
		}
		catch (PDOException $e) {
			$this->debug(__FUNCTION__, $sql, $e);
			if (!$this->isDebug())
				throw $e;
		}
	}

	public function query($sql, array $params = array(), array $options = array()) {

		$stmt = $this->prepare($sql);

		try {
			if ($params)
				$this->bindValues($stmt, $params);
			$stmt->execute();

			if ($this->isDebug())
				$this->debug(__FUNCTION__, $stmt);
			return $stmt;
		}
		catch (PDOException $e) {
			$this->debug(__FUNCTION__, $stmt, $e);
			if (!$this->isDebug())
				throw $e;
		}
	}
	
	public function exec($sql, array $params = array(), array $options = array()) {

		$stmt = $this->prepare($sql, $options);

		try {
			if ($params)
				$this->bindValues($stmt, $params);

			$stmt->execute();
			$stmt->rowCount = $stmt->rowCount();

			if ($this->isDebug())
				$this->debug(__FUNCTION__, $stmt);
			return $stmt->rowCount;
		}
		catch (PDOException $e) {
			$this->debug(__FUNCTION__, $stmt, $e);
			if (!$this->isDebug())
				throw $e;
		}
		finally {
			$stmt->closeCursor();
		}

	}

	public function bindValues(PDOStatement &$stmt, array $params) {
		try {
			if ($this->isDebug())
				$bindInfo = "bindValues():\n";
	
			$keys = array_keys($params);
			foreach ($keys as $key) {
				$p = (preg_match('/^:/', $key))? $key: ":{$key}";
				$bindInfo .= "{$p}=";

				if (is_array($params[$key])) {
					$stmt->bindValue($p, $params[$key][0], $params[$key][1]);
					if ($this->isDebug())
						$bindInfo .= "{$params[$key][0]} (" . self::getParamTypeName($params[$key][1]) . ")\n";
				}
				else {
					$type = self::getParamType($params[$key]);
					$stmt->bindValue($p, $params[$key], $type);
					if ($this->isDebug())
						$bindInfo .= "{$params[$key]} (" . self::getParamTypeName($type) . ")\n";
				}
			}

			if ($this->isDebug())
				$stmt->bindInfo = $bindInfo;
		}
		catch (PDOException $e) {
			$this->debug(__FUNCTION__, $stmt, $e);
			if (!$this->isDebug())
				throw $e;
		}
	}

	public function fetchList(PDOStatement $stmt) {
		try {
			$list = $stmt->fetchAll(PDO::FETCH_ASSOC);
			return $list;
		}
		catch (PDOException $e) {
			$this->debug(__FUNCTION__, $stmt, $e);
			if (!$this->isDebug())
				throw $e;
		}
		finally {
			$stmt->closeCursor();
		}
	}
	
	public function fetchRow(PDOStatement $stmt) {
		try {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			return $row;
		}
		catch (PDOException $e) {
			$this->debug(__FUNCTION__, $stmt, $e);
			if (!$this->isDebug())
				throw $e;
		}
		finally {
			$stmt->closeCursor();
		}
	}
	
	public function fetchValue(PDOStatement $stmt) {
		try {
			$value = $stmt->fetchColumn(0);
			return $value;
		}
		catch (PDOException $e) {
			$this->debug(__FUNCTION__, $stmt, $e);
			if (!$this->isDebug())
				throw $e;
		}
		finally {
			$stmt->closeCursor();
		}
	}

	/*
	 * high level
	 */

	public function queryList($sql, array $params = array(), array $options = array()) {
		$stmt = $this->query($sql, $params, $options);
		return ($stmt)? $this->fetchList($stmt): array();
	}
	
	public function queryRow($sql, array $params = array(), array $options = array()) {
		$stmt = $this->query($sql, $params, $options);
		return ($stmt)? $this->fetchRow($stmt): array();
	}
	
	public function queryValue($sql, array $params = array(), array $options = array()) {
		$stmt = $this->query($sql, $params, $options);
		return ($stmt)? $this->fetchValue($stmt): null;
	}

	/*
	 * simple wrapper to PDO
	 */

	/**
	 * @return bool
	 * @see PDO::beginTransaction()
	 */
	public function beginTransaction() {
		$pdo = $this->connect();
		return $pdo->beginTransaction();
	}
	
	/**
	 * @return bool
	 * @see PDO::commit()
	 */
	public function commit() {
		$pdo = $this->connect();
		return $pdo->commit();
	}
	
	/**
	 * @return bool
	 * @see PDO::rollBack()
	 */
	public function rollBack() {
		$pdo = $this->connect();
		return $pdo->rollBack();
	}
	
	/**
	 * @return bool
	 * @see PDO::inTransaction()
	 */
	public function inTransaction() {
		$pdo = $this->connect();
		return $pdo->inTransaction();
	}
	
	/**
	 * @param string $name
	 * @return mixed
	 * @see PDO::lastInsertId()
	 */
	public function lastInsertId($name = null) {
		$pdo = $this->connect();
		return $pdo->lastInsertId($name);
	}
	
	/**
	 * @param string $string
	 * @param int $parameterType
	 * @retrun string
	 */
	public function quote($string, $parameterType = PDO::PARAM_STR) {
		$pdo = $this->connect();
		return $pdo->quote($string, $parameterType);
	}

	/*
	 * static utilities
	 */

	/**
	 * get PDO param type by PHP data type
	 */
	public static function getParamType($value) {
		if ($value === null)
			return PDO::PARAM_NULL;
		else if (is_int($value))
			return PDO::PARAM_INT;
		else if (is_bool($value))
			return PDO::PARAM_BOOL;
		else
			return PDO::PARAM_STR;
	}

	public static function getParamTypeName($type) {
		$typeName = "";

		$type1 = $type & ~PDO::PARAM_INPUT_OUTPUT;
		$type2 = $type & PDO::PARAM_INPUT_OUTPUT;

		switch ($type1) {
		case PDO::PARAM_NULL:
			$typeName = "PARAM_NULL";
			break;
		case PDO::PARAM_INT:
			$typeName = "PARAM_INT";
			break;
		case PDO::PARAM_BOOL:
			$typeName = "PARAM_BOOL";
			break;
		case PDO::PARAM_STR:
			$typeName = "PARAM_STR";
			break;
		case PDO::PARAM_LOB:
			$typeName = "PARAM_LOB";
			break;
		case PDO::PARAM_STMT:
			$typeName = "PARAM_STMT";
			break;
		default:
			$typeName = "(unknown)";
		}

		if ($type2)
			$typeName .= "|PARAM_INPUT_OUTPUT";

		return $typeName;
	}

	public static function mergeParams(&$params, array $additionalParams) {
		foreach ($additionalParams as $column => $value) {
			if (!isset($params[$column]))
				$params[$column] = $value;
		}
	}

	/*
	 * for debug
	 */

	public function isDebug() {
		return $this->debug;
	}
	
	public function setDebug($debug) {
		$this->debug = $debug;
	}

	protected function debug($func, $obj = null, $exception = null) {
		$msg = get_class($this) . '::' . $func . "()";
		if ($exception)
			$msg .= ": " . $exception->getMessage();

		if ($obj instanceof PDOStatement) {
			$msg .= ": {$obj->queryString}";
			if ($obj->bindInfo)
				$msg .= "\nBinded using {$obj->bindInfo}";
		}
		else {
			$msg .= ": {$obj}";
		}

		if ($this->isDebug()) {
			echo $msg . "\n";
		}
		else {
			error_log($msg);
		}
	}

}