<?php

namespace Yidigun;

use Yidigun\Util\ArrayParams;

class Get extends ArrayParams {

	public function __construct() {
		parent::__construct($_GET);
	}

}
