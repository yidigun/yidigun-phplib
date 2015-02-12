<?php

use Yidigun\DB\DBManager;
use Yidigun\DB\SQLBuilder;
use Yidigun\WebFilter;
use Yidigun\WebSearch;
use Yidigun\WebList;

/*

DROP TABLE IF EXISTS yidigun_example;
CREATE TABLE yidigun_example (
	no			INT NOT NULL auto_increment,
	name		VARCHAR(50) NOT NULL,
	company		VARCHAR(50),
	email		VARCHAR(50),
	phone		VARCHAR(20),
	sex			CHAR(1),
	reg_date	DATETIME,
	mod_date	DATETIME,
	PRIMARY KEY (no)
) Engine=MyISAM DEFAULT CHARSET=utf8;

 */
class YidigunExampleManager extends DBManager {

	private $table = 'yidigun_example';
	private $from = "yidigun_example AS S";

	public function __construct(){
		parent::__construct("exampledb");
	}

	/*
	 * select filters
	 */

	protected function setWhere(WebFilter $f, SQLBuilder &$builder, array &$params) {

		foreach ($f->filters as $name => $query) {
			if ($query) {
				if ($name == 'sex') {
					$builder->where("S.sex = :sex");
					$params['sex'] = $query;
				}
				elseif ($name == 'name') {
					$builder->where("S.name LIKE CONCAT('%', :name, '%')");
					$params['name'] = $query;
				}
				elseif ($name == 'company') {
					$builder->where("S.company LIKE CONCAT('%', :company, '%')");
					$params['company'] = $query;
				}
				elseif ($name == 'phone') {
					$builder->where("S.phone LIKE CONCAT('%', :phone, '%')");
					$params['phone'] = $query;
				}
				elseif ($name == 'email') {
					$builder->where("S.email LIKE CONCAT('%', :email, '%')");
					$params['email'] = $query;
				}
			}
		}
	}

	protected function setLimit(WebFilter $f, SQLBuilder &$builder, array &$params) {

		$builder->limit(':startNo', ':pageSize');

		$params['startNo'] = $f->startNo;
		$params['pageSize'] = $f->pageSize;
	}

	/*
	 * application operations
	 */

	public function getCount(WebFilter $f, array $options = array()) {

		$builder = $this->newSQLBuilder();
		
		$builder->select('COUNT(*)')
			->from($this->from);

		$params = array();
		$this->setWhere($f, $builder, $params);

		return $this->queryValue($builder, $params, $options);
	}

	public function getListPage(WebFilter $f, array $options = array()) {

		$builder = $this->newSQLBuilder();
		
		$builder->select('S.*')
			->from($this->from)
			->orderBy("S.no DESC");

		$params = array();
		$this->setWhere($f, $builder, $params);
		$this->setLimit($f, $builder, $params);

		return $this->queryList($builder, $params, $options);
	}

	public function getWebList(WebFilter $f, array $options = array()) {

		$webList = new WebList($f);

		$webList->count = $this->getCount($f, $options);

		$webList->list = $this->getListPage($webList, $options);

		return $webList;
	}

	public function getListAll(WebFilter $f, array $options = array()) {

		$builder = $this->newSQLBuilder();
		
		$builder->select('S.*')
			->from($this->from);

		$params = array();
		$this->setWhere($f, $builder, $params);

		return $this->query($builder, $params, $options);
	}

	public function getRow($no, array $options = array()) {

		$builder = $this->newSQLBuilder();
		
		$builder->select()
			->from($this->from)
			->where('no = :no');

		$params = array(
			'no'	=> $no,
		);
		return $this->queryRow($builder, $params, $options);
	}

	/*
	 * unique alt key checking
	 */
	public function getRowByPhone($phone, array $options = array()) {

		$builder = $this->newSQLBuilder();
		
		$builder->select()
			->from($this->from)
			->where('phone = :phone');

		$params = array(
			'phone'	=> $phone,
		);
		return $this->queryRow($builder, $params, $options);
	}

	public function insertRow(array $array, array $options = array()) {

		$builder = $this->newSQLBuilder();

		$params = array(
			'no'		=> null,
			'name'		=> $array['name'],
			'company'	=> $array['company'],
			'email'		=> $array['email'],
			'phone'		=> $array['phone'],
			'sex'		=> $array['sex'],
			'reg_date'	=> date('Y-m-d H:i:s'),
			'mod_date'	=> date('Y-m-d H:i:s'),
		);

		$builder->insert($this->table)
			->columnParams(array_keys($params));

		$rs = $this->exec($builder, $params, $options);
		return $this->lastInsertId();
	}

	public function updateRow($no, array $array, array $options = array()) {

		$builder = $this->newSQLBuilder();

		$params = array(
			'name'		=> $array['name'],
			'company'	=> $array['company'],
			'email'		=> $array['email'],
			'phone'		=> $array['phone'],
			'sex'		=> $array['sex'],
			'mod_date'	=> date('Y-m-d H:i:s'),
		);

		$builder->update($this->table)
			->columnParams(array_keys($params))
			->where("no = :no");

		$params['no'] = $no;

		return $this->exec($builder, $params, $options);
	}

	public function deleteRow($no, array $options = array()) {

		$builder = $this->newSQLBuilder();

		$builder->delete($this->table)
			->where("no = :no");

		$params = array(
			'no'		=> $no,
		);

		return $this->exec($builder, $params, $options);
	}
}
