<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun;

/**
 * UTF-8 Utilities.
 * This class implements low-level algorithm.
 * Some features of this class can be replaced with mbstring functions.
 * @see http://helloworld.naver.com/helloworld/76650
 */
class UTF8 {

	public static function hasBOM($str) {
		return (ord($str{0}) == 0xEF &&
			ord($str{0}) == 0xBB &&
			ord($str{0}) == 0xBF);
	}

	/**
	 * ord() for utf-8 encoded string.
	 * returns unicode value of the character starts at given index.
	 */
	public static function ord($c, $byte_index = 0) {
		$v = ord($c{$byte_index});
		// U+0080 ~ U+07FF: 2bytes, 11bits 110x xxxx 10xx xxxx
		if (($v & 0xe0) == 0xc0) {
			return (
				((ord($c{$byte_index}) & 0x1f) << 6) |
				(ord($c{$byte_index + 1}) & 0x3f)
			);
		}
		// U+0800 ~ U+FFFF: 3bytes, 16bits 1110 xxxx 10xx xxxx 10xx xxxx
		else if (($v & 0xf0) == 0xe0) {
			return (
				((ord($c{$byte_index}) & 0x0f) << 12) |
				(ord($c{$byte_index + 1}) & 0x3f) << 6 |
				(ord($c{$byte_index + 2}) & 0x3f)
			);
		}
		// U+10000 ~ U+1FFFFF: 4bytes, 21bits 1111 0xxx 10xx xxxx 10xx xxxx 10xx xxxx
		else if (($v & 0xf8) == 0xf0) {
			return (
				((ord($c{$byte_index}) & 0x07) << 18) |
				(ord($c{$byte_index + 1}) & 0x3f) << 12 |
				(ord($c{$byte_index + 2}) & 0x3f) << 6 |
				(ord($c{$byte_index + 3}) & 0x3f)
			);
		}
		// U+0000 ~ U+007F: 1byte, 7bits (0xxxx xxxx)
		else {
			return $v;
		}
	}

	/**
	 * chr() for unicode number.
	 * returns utf-8 encoded character.
	 */
	public static function chr($u) {
		// U+0080 ~ U+07FF: 2bytes, 11bits 110x xxxx 10xx xxxx
		if ($u >= 0x0080 && $u <= 0x07FF) {
			$s = chr($u & 0x3F | 0x80);
			$s = chr(($u >>= 6) & 0x1F | 0xC0) . $s;
		}
		// U+0800 ~ U+FFFF: 3bytes, 16bits 1110 xxxx 10xx xxxx 10xx xxxx
		else if ($u >= 0x0800 && $u <= 0xFFFF) {
			$s = chr($u & 0x3F | 0x80);
			$s = chr(($u >>= 6) & 0x3F | 0x80) . $s;
			$s = chr(($u >>= 6) & 0xF | 0xE0) . $s;
		}
		// U+10000 ~ U+1FFFFF: 4bytes, 21bits 1111 0xxx 10xx xxxx 10xx xxxx 10xx xxxx
		else if ($u >= 0x10000 && $u <= 0x1FFFFF) {
			$s = chr($u & 0x3F | 0x80);
			$s = chr(($u >>= 6) & 0x3F | 0x80) . $s;
			$s = chr(($u >>= 6) & 0x3F | 0x80) . $s;
			$s = chr(($u >>= 6) & 0x7 | 0xF0) . $s;
		}
		else {
			$s = chr($u);
		}
		return $s;
	}

	public static function getBytes($c, $byte_index = 0) {
		$v = ord($c{$byte_index});
		// U+0080 ~ U+07FF: 2bytes, 11bits 110x xxxx 10xx xxxx
		if (($v & 0xe0) == 0xc0)
			return 2;
		// U+0800 ~ U+FFFF: 3bytes, 16bits 1110 xxxx 10xx xxxx 10xx xxxx
		else if (($v & 0xf0) == 0xe0)
			return 3;
		// U+10000 ~ U+1FFFFF: 4bytes, 21bits 1111 0xxx 10xx xxxx 10xx xxxx 10xx xxxx
		else if (($v & 0xf8) == 0xf0)
			return 4;
		// U+0000 ~ U+007F: 1byte, 7bits (0xxxx xxxx)
		else
			return 1;
	}

	public static function char($str, $byte_index = 0) {
		$strlen = strlen($str);
		$len = self::getBytes($str, $byte_index);
		return ($strlen == $len)? str: substr($str, $byte_index, $len);
	}

	/**
	 * index 번째 글짜를 리턴한다.
	 * BOM을 가진 경우 3바이트를 skip하고 시작한다.
	 */
	public static function charAt($str, $index) {
		$pos = (self::hasBOM($s))? 3: 0;
		$char = '';
		self::map(function($c, $u) use (&$pos, &$char, $index) {
			if ($pos == $index) {
				$char = $c;
				return false;
			}
			$pos++;
		}, $str);
		return $char;
	}

	/**
	 * UTF-8 문자열을 순회한다. 매 글자당 callback 함수가 실행된다.
	 * BOM을 가진 경우 3바이트를 skip하고 시작한다.
	 *
	 * UTF8::map(function($c, $u) use ($some) { ... }, $str);
	 */
	public static function map(callable $callback, $s) {
		$strlen = strlen($s);
		$pos = (self::hasBOM($s))? 3: 0;
		while ($pos < $strlen) {
			$len = self::getBytes($s, $pos);
			$c = substr($s, $pos, $len);
			$u = self::ord($s, $pos);
			$rs = $callback($c, $u);
			if ($rs === false)
				break;
			$pos += $len;
		}
	}

}
