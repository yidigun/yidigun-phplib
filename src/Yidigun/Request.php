<?php
namespace Yidigun;

use Yidigun\Util\HttpParams;

class Request extends HttpParams {

	public function __construct() {
		parent::__construct($_REQUEST);
	}
}
