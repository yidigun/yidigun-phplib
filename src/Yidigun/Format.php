<?php
namespace Yidigun;

/**
 * text formatting utilities.
 */
class Format {

	public static function html($value, $default = '') {
		if (!$value)
			return $default;
		else
			return nl2br($value);
	}

	public static function text($value, $default = '') {
		if (!$value)
			return $default;
		else
			return nl2br(htmlspecialchars($value));
	}

	public static function number($value, $decimals = 0, $default = '') {
		if ($value === 0 || $value === 0.0 || $value == '0')
			return "0";
		elseif (!$value)
			return $default;
		else
			return number_format($value, $decimals);
	}

	public static function date($value, $format = 'auto', $default = '') {
		if (!$value || $value == '0000-00-00' || $value == '0000-00-00 00:00:00')
			return $default;

		$ts = strtotime($value);
		if ($format == 'auto') {
			$now = time();
			return ($ts > $now - 86400)? date('H:i:s', $ts): date('Y-m-d', $ts);
		}
		elseif (is_array($format)) {
			$now = time();
			return ($ts > $now - 86400)? date($foramt['time'], $ts): date($foramt['date'], $ts);
		}
		else {
			return date($format, $ts);
		}
	}

	public static function code($value, array $codes, $default = '') {
		return ($codes[$value])? $codes[$value]: $default;
	}

	public static function link($link, array $attrs = array(), $default = '') {

		if (is_array($link)) {
			$scheme = $link[0];
			$link = $link[1];
		}

		if (!$link)
			return $default;

		$href = ($scheme)? "{$scheme}:{$link}": $link;
		$attr = "";
		foreach ($attrs as $name => $value) {
			$attr .= " {$name}=\"" . htmlspecialchars($value) . "\"";
		}
		return "<a href=\"{$href}\"{$attr}>{$link}</a>";
	}

}
