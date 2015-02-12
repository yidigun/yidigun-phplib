<?php
namespace Yidigun;

use Yidigun\Util\ArrayParams;

class Server extends ArrayParams {

	public function __construct() {
		parent::__construct($_SERVER);
	}

	public static function getRemoteAddr() {
		return (isset($_SERVER['HTTP_X_FORWARDED_FOR']))?
					$_SERVER['HTTP_X_FORWARDED_FOR']:
					$_SERVER['REMOTE_ADDR'];
	}

}
