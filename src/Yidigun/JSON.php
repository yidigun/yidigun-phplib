<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun;

/**
 * Yidigun\JSON
 * JSON utility functions
 */
class JSON {
	
	public static $SUCCESS = 0;
	public static $ERROR = -1;

	public static function write($json) {
		Header("Content-Length: " . strlen($json));

		ob_end_clean();
		echo $json;
		exit();
	}

	public static function success($result, $code = null) {
		$response = array(
			'result'	=> $result,
			'code'		=> (($code != null)? $code: self::$SUCCESS),
		);
		self::write(self::encode($response));
	}

	public static function error($msgOrEx, $code = null, $exception = null) {
		$response = array(
			'message'	=> (($msgOrEx instanceof Exception)? $msgOrEx->getMessage(): $msgOrEx),
			'code'		=> (($code != null)? $code: self::$ERROR),
		);
		if ($exception)
			$response['debug'] = $exception->getMessage();
		self::write(self::encode($response));
	}

	/**
	 * alias of json_encode()
	 */
	public static function encode($obj, $options = 0, $depth = 512) {
		return json_encode($obj, $options, $depth);
	}

	/**
	 * alias of json_decode()
	 * set $assoc = true by default.
	 */
	public static function decode($json, $assoc = true, $depth = 512, $options = 0) {
		return json_decode($json, $assoc, $depth, $options);
	}

}
