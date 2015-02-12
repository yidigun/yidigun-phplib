<?php
namespace Yidigun;

use Yidigun\Util\ArrayParams;

class Session extends ArrayParams {

	public function __construct() {
		parent::__construct($_SESSION);
		session_start();
	}

}
