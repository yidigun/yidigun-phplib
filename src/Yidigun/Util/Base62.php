<?php

namespace Yidigun\Util;

class Base62 {

	public static $CHARS = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

	public static function toDec($b62) {
		$b62 = strrev($b62);
		$len = strlen($b62);
		$dec = 0;
		for ($i = 0; $i < $len; $i++) {
			$strpos = strpos(self::$CHARS, $b62[$i]);
			$dec += strpos(self::$CHARS, $b62[$i]) * pow(62, $i);
		}
		return $dec;
	}

	public static function toBase62($dec) {
		$tmp1 = (int)($dec/62);
		$tmp2 = $dec % 62;
		$b62 = self::$CHARS{$tmp2};
		if ($tmp1)
			$b62 = self::toBase62($tmp1) . $b62;
		return $b62;
	}
}
