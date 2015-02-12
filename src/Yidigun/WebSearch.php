<?php

namespace Yidigun;

class WebSearch implements WebFilter {

	private $filters = array();
	private $pageNo = 1;
	private $pageSize = 10;
	protected $fields; 

	public function __construct(array $fields = array()) {
		$this->fields = $fields;
	}

	public static function newWebSearch(array $init) {
		$webSearch = new WebSearch(($init['fields'])? $init['fields']: array());
		$webSearch->pageNo = ($init['pageNo'])? intval($init['pageNo']): 1;
		$webSearch->pageSize = ($init['pageSize'])? intval($init['pageSize']): 1;
		$webSearch->filters = ($init['filters'])? $init['filters']: array();
		return $webSearch;
	}

	public function __get($name) {
		if ($name == 'pageNo' || $name == 'pageSize' || $name == 'filters' ||
				in_array($name, $this->fields)) {
			return $this->$name;
		}
		elseif ($name == 'startNo') {
			return $this->getStartNo();
		}
		elseif ($name == 'filterString') {
			return $this->getFilterString();
		}
	}

	public function __set($name, $value) {
		if ($name == 'pageSize' ||
				in_array($name, $this->fields)) {
			$this->$name = $value;
		}
		elseif ($name == 'pageNo') {
			$this->setPageNo($value);
		}
	}
	
	/*
	 * implements WebFilter
	 */

	public function getPageNo() {
		return $this->pageNo;
	}

	public function setPageNo($pageNo) {
		$this->pageNo = ($pageNo < 1)? 1: intval($pageNo);
	}

	public function getPageSize() {
		return $this->pageSize;
	}

	public function setPageSize($pageSize) {
		$this->pageSize = intval($pageSize);
	}

	public function getFilters() {
		return $this->filters;
	}

	public function setFilters($filters) {
		$this->filters = $filters;
	}

	public function filter($name, $query) {
		if ($query)
			$this->filters[$name] = $query;
	}

	public function getStartNo() {
		return intval(($this->pageNo - 1) * $this->pageSize);
	}

	public function getFilterString() {
		$arr = array();
		foreach ($this->filters as $name => $query) {
			$arr[] = $name . "=" . $query;
		}
		return implode(',', $arr);
	}
}
