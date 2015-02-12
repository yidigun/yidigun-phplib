<?php

namespace Yidigun\Util;

abstract class HttpParams extends ArrayParams {

	public function file($name) {

		return (isset($_FILES[$name]))?
					$_FILES[$name]:
					array('error' => UPLOAD_ERR_NO_FILE);
	}

}
