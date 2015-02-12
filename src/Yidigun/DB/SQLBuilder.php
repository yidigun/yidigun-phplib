<?php

namespace Yidigun\DB;

abstract class SQLBuilder {

	/*
	 * factory methods
	 */

	protected function __construct($scheme) {
		$this->scheme = $scheme;
	}

	public static function newSQLBuilder($scheme) {
		return self::loadClass($scheme);
	}

	protected static function loadClass($scheme) {
		$class = "\\" . __NAMESPACE__ . "\\SQLBuilders\\" . strtolower($scheme);

		// try load contrete class
		//\__autoload($class);
		if (!class_exists($class))
			throw new \Exception("SQLBuilder class is not found: {$class}");

		return new $class($scheme);
	}

	/*
	 * db type (driver)
	 */

	protected $scheme;

	public function getType() {
		return $this->scheme;
	}

	/*
	 * query type
	 */

	const SQL_NONE = 0;
	const SQL_SELECT = 1;
	const SQL_INSERT = 2;
	const SQL_UPDATE = 3;
	const SQL_DELETE = 4;

	protected $queryType = 0;

	public function getQueryType() {
		return $this->queryType;
	}

	/*
	 * build method
	 */

	public function buildQuery() {
		switch ($this->queryType) {
		case self::SQL_SELECT:
			$sql = $this->buildSelectQuery();
			$this->reset();
			return $sql;
		case self::SQL_INSERT:
			$sql = $this->buildInsertQuery();
			$this->reset();
			return $sql;
		case self::SQL_UPDATE:
			$sql = $this->buildUpdateQuery();
			$this->reset();
			return $sql;
		case self::SQL_DELETE:
			$sql = $this->buildDeleteQuery();
			$this->reset();
			return $sql;
		default:
			$this->reset();
			return "/* unknown query type */";
		}
	}

	public function __toString() {
		return $this->buildquery();
	}

	/*
	 * select()
	 * ->from()
	 * ->where()
	 * ->groupBy()
	 * ->having()
	 * ->orderBy()
	 */

	protected function buildSelectQuery() {

		extract($this->parts);

		$sql = "SELECT ";
		$sql .= (is_array($columns))? implode(', ', $columns): $columns;

		if ($from)
			$sql .= $this->nl() . "FROM {$from}";

		if ($where)
			$sql .= $this->nl() . "WHERE " . ((is_array($where))? '(' . implode(') AND (', $where) . ')': $where);

		if ($groupBy) {
			$sql .= $this->nl() . "GROUP BY " . ((is_array($groupBy))? implode(', ', $groupBy): $groupBy);
			if ($having)
				$sql .= $this->nl() . "HAVING " . ((is_array($having))? '(' . implode(') AND (', $having) . ')': $having);
		}

		if ($orderBy)
			$sql .= $this->nl() . "ORDER BY " . ((is_array($orderBy))? implode(', ', $orderBy): $orderBy);

		return $sql;
	}

	/*
	 * insert()
	 * ->values()
	 *
	 * insert()
	 * ->columns()
	 * ->values()
	 *
	 * insert()
	 * ->columnValues()
	 */

	protected function buildInsertQuery() {

		extract($this->parts);

		$sql = "INSERT INTO {$this->parts['table']}";

		if ($columnParams) {
			$colList = '';
			$valList = '';
			$len = count($columnParams);
			$i = 0;
			foreach ($columnParams as $column) {
				$colList .= $column;
				$valList .= ":" . $column;
				if ($i < $len - 1) {
					$colList .= ', ';
					$valList .= ', ';
				}
				$i++;
			}
			$sql .= " ({$colList})";
			$sql .= $this->nl() . "VALUES ({$valList})";
		}
		elseif ($columnValues) {
			$colList = '';
			$valList = '';
			$len = count($columnValues);
			$i = 0;
			foreach ($columnValues as $column => $value) {
				$colList .= $column;
				$valList .= ($value == null)? 'NULL': $value;
				if ($i < $len - 1) {
					$colList .= ', ';
					$valList .= ', ';
				}
				$i++;
			}
			$sql .= " ({$colList})";
			$sql .= $this->nl() . "VALUES ({$valList})";
		}
		else if ($values) {
			$sql .= $this->nl() . "VALUES (";
			foreach ($values as $column => $value) {
				$sql .= ($value == null)? 'NULL': $value;
				if ($i < $len - 1)
					$sql .= ', ';
				$i++;
			}
			$sql .= ")";
		}

		return $sql;
	}

	/*
	 * update()
	 * ->set()
	 * ->columnValue()
	 * ->where()
	 *
	 * update()
	 * ->setArray()
	 * ->columnValues()
	 * ->where()
	 */

	protected function buildUpdateQuery() {

		extract($this->parts);

		$sql = "UPDATE {$this->parts['table']}";
		$sql .= $this->nl() . "SET ";

		if ($columnParams) {
			$len = count($columnParams);
			$i = 0;
			foreach ($columnParams as $column) {
				$sql .= $this->tab() . $column . " = :" . $column;
				if ($i < $len - 1)
					$sql .= "," . $this->nl();
				$i++;
			}
		}
		elseif ($columnValues) {
			$len = count($columnValues);
			$i = 0;
			foreach ($columnValues as $column => $value) {
				$sql .= $this->tab() . $column . " = " . (($value == null)? 'NULL': $value);
				if ($i < $len - 1)
					$sql .= "," . $this->nl();
				$i++;
			}
		}

		if ($where)
			$sql .= $this->nl() . "WHERE " . ((is_array($where))? '(' . implode(') AND (', $where) . ')': $where);
		else
			$sql .= $this->nl() . "WHERE (0 = 1)"; /* prevent update whole table */

		return $sql;
	}

	/*
	 * delete()
	 * ->where()
	 */

	protected function buildDeleteQuery() {

		extract($this->parts);

		$sql = "DELETE FROM {$this->parts['table']}";

		if ($where)
			$sql .= $this->nl() . "WHERE " . ((is_array($where))? '(' . implode(') AND (', $where) . ')': $where);
		else
			$sql .= $this->nl() . "WHERE (0 = 1)"; /* prevent delete whole table */

		return $sql;
	}

	/*
	 * building methods
	 */

	protected $parts = array();

	public function reset() {
		$this->queryType = 0;
		$this->parts = array();
		return $this;
	}

	protected function addParts($part, $spec) {
		if ($this->parts[$part]) {
			if (!is_array($this->parts[$part]))
				$this->parts[$part] = array($this->parts[$part]);
			$this->parts[$part] = array_merge($this->parts[$part], ((is_array($spec))? $spec: array($spec)));
		}
		else {
			$this->parts[$part] = $spec;
		}
	}

	public function select($columnSpec = '*') {
		$this->queryType = self::SQL_SELECT;
		$this->parts = array();
		$this->parts['columns'] = $columnSpec;
		return $this;
	}

	public function columns($columnSpec) {
		$this->addParts('columns', $columnSpec);
		return $this;
	}

	public function setColumns($columnSpec) {
		unset($this->parts['columns']);
		$this->addParts('columns', $columnSpec);
		return $this;
	}

	public function from($fromSpec) {
		$this->addParts('from', $fromSpec);
		return $this;
	}

	public function where($whereSpec) {
		$this->addParts('where', $whereSpec);
		return $this;
	}

	public function groupBy($groupBySpec) {
		$this->addParts('groupBy', $groupBySpec);
		return $this;
	}

	public function having($havingSpec) {
		$this->addParts('having', $havingSpec);
		return $this;
	}

	public function orderBy($orderBySpec) {
		$this->addParts('orderBy', $orderBySpec);
		return $this;
	}

	public function insert($tableSpec) {
		$this->queryType = self::SQL_INSERT;
		$this->parts = array();
		$this->parts['table'] = $tableSpec;
		return $this;
	}

	public function values($valuesSpec) {
		if ($this->parts['columns']) {
			$values = (is_array($valuesSpec))? $valuesSpec: array($valuesSpec);
			if (!$this->parts['columnValues'])
				$this->parts['columnValues'] = array();
			foreach ($values as $value) {
				$column = array_shift($this->parts['columns']);
				if ($column)
					$this->parts['columnValues'][$column] = $value;
			}
		}
		else {
			$this->addParts('values', $valuesSpec);
		}
		return $this;
	}

	public function columnValue($column, $value) {
		$this->addParts('columnValues', array($column => $value));
		return $this;
	}

	public function columnValues($columnValuesSpec) {
		$this->addParts('columnValues', $columnValuesSpec);
		return $this;
	}

	public function columnParams($columnParamsSpec) {
		$this->addParts('columnParams', $columnParamsSpec);
		return $this;
	}

	/**
	 * alias of columnValue()
	 */
	public function set($column, $value) {
		return $this->columnValue($column, $value);
	}

	/**
	 * alias of columnValues()
	 */
	public function setArray($columnValuesSpec) {
		return $this->columnValues($columnValuesSpec);
	}

	public function update($tableSpec) {
		$this->queryType = self::SQL_UPDATE;
		$this->parts = array();
		$this->parts['table'] = $tableSpec;
		return $this;
	}

	public function delete($tableSpec) {
		$this->queryType = self::SQL_DELETE;
		$this->parts = array();
		$this->parts['table'] = $tableSpec;
		return $this;
	}

	/*
	 * for debug
	 */

	protected $debug = false;

	public function isDebug() {
		return $this->debug;
	}

	public function setDebug($debug) {
		$this->debug = $debug;
	}

	protected function nl() {
		return ($this->isDebug())? "\n": " ";
	}

	protected function tab() {
		return ($this->isDebug())? "\t": "";
	}
}
