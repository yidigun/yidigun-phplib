<?php


namespace Yidigun\DB;

class PDOConnectionException extends \PDOException {

	public $errorInfo;
	public $ds;

	public function __construct($previous, DataSource $ds = null) {
		parent::__construct($previous->getMessage(), $previous->getCode(), $previous);
		$this->errorInfo = $previous->errorInfo;
		$this->ds = $ds;
	}

}
