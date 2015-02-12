<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun;

/**
 * Yidigun\MIME
 * MIME utility functions
 */
class MIME {

	public static $MIME_ENCODE_PREF = array(
		"input-charset"		=> "UTF-8",
		"output-charset"	=> "UTF-8",
		"line-length"		=> 76,
		"line-break-chars"	=> "\r\n",
		"scheme"			=> "B",
	);

	public function fileType($filename) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);

		$contentType = finfo_file($finfo, $filename);

		finfo_close($finfo);
		return $contentType;
	}

	public function textType($str) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);

		$contentType = finfo_buffer($finfo, $str);

		finfo_close($finfo);
		return $contentType;
	}

	public function header($name, $value, $encode = false) {
		return ($encode)?
				iconv_mime_encode($name, $value, self::$MIME_ENCODE_PREF):
				wordwrap("{$name}: {$value}", 75, "\r\n" . " ", false);
	}
	
}
