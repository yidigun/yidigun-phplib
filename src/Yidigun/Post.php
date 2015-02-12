<?php
namespace Yidigun;

use Yidigun\Util\HttpParams;

class Post extends HttpParams {

	public function __construct() {
		parent::__construct($_POST);
	}
}
