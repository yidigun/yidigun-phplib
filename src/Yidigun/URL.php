<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun;

/**
 * Yidigun\URL
 * URL utility functions
 */
class URL {

	public static $DEFAULT_PORT = array(
		'http'		=> 80,
		'https'		=> 443,
		'mysql'		=> 3306,
	);

	/**
	 * wrapper of parse_url()
	 */
	public static function parse($url, $component = -1) {
		return parse_url($url, $component);
	}

	/**
	 * reverse function of parse_url()
	 */
	public static function make($parsed) {

		$s = '';

		// scheme
		if ($parsed['scheme'])
			$s .= $parsed['scheme'] . ':';

		// host
		if ($parsed['host']) {
			if ($parsed['user']) {
				$s .= $parsed['user'];
				if ($parsed['pass'])
					$s .= ":" . $parsed['pass'];
				$s .= "@";
			}

			// host
			$s .= "//" . $parsed['host'];

			// port
			if ($parsed['port'] &&
					self::$DEFAULT_PORT[$parsed['scheme']] != $parsed['port']) {
				$s .= ':' . $parsed['port'];
			}
		}

		// path
		if (!$parsed['path'])
			$s .= "/";
		else
			$s .= $parsed['path'];

		// query
		if ($parsed['query']) {
			if (is_array($parsed['query'])) {
				$len = count($parsed['query']);
				$qq = array();
				foreach ($parsed['query'] as $key => $value) {
					if ($value) {
						$qq[] = urlencode($key) . '=' . urlencode($value);
					}
				}
				$s .= "?" . implode('&', $qq);
			}
			else {
				$s .= '?' . $parsed['query'];
			}
		}
		// fragment
		if ($parsed['fragment'])
			$s .= '#' . $parsed['fragment'];

		return $s;
	}

	public static function current() {		
		return self::make(self::currentInfo());
	}

	public static function currentInfo() {		
		return array(
			'scheme'	=> ($_SERVER['HTTPS'] == 'on')? 'https': 'http',
			'host'		=> $_SERVER['SERVER_NAME'],
			'port'		=> $_SERVER['SERVER_PORT'],
			'path'		=> (($pos = strpos($_SERVER['REQUEST_URI'], '?')) === FALSE)?
								$_SERVER['REQUEST_URI']:
								substr($_SERVER['REQUEST_URI'], 0, $pos),
			'query'		=> $_SERVER['QUERY_STRING'],
		);
	}

	public static function abs($url, $baseURL = null) {
		$parsed = parse_url($url);
		if ($parsed['scheme'])
			return $url;

		if ($baseURL == null)
			$base = self::currentInfo();
		else
			$base = parse_url($baseURL);

		$parsed['scheme'] = $base['scheme'];
		$parsed['host'] = $base['host'];
		$parsed['port'] = $base['port'];
		$parsed['user'] = $base['user'];
		$parsed['pass'] = $base['pass'];

		if (!preg_match('!^/!', $parsed['path'])) {
			$basePath = (preg_match('!/$!', $base['path']))?
							$base['path']:
							dirname($base['path']);
			$parsed['path'] = File::abs($parsed['path'], $basePath);
		}
		return self::make($parsed);
	}

	public static function build($url, $params) {
		$p = parse_url($url);

		parse_str($p['query'], $query);

		if (is_array($params)) {
			foreach ($params as $key => $value) {
				if (isset($query[$key]))
					continue;
				$query[$key] = $value;
			}
		}

		$len = count($query);
		$arr = array();
		foreach ($query as $key => $value) {
			if ($value)
				$arr[] = urlencode($key) . "=" . urlencode($value);
		}
		$s = implode('&', $arr);

		if ($params && !is_array($params))
			$s = (($s)? "&": "") . $params;

		$p['query'] = $s;

		return self::make($p);
	}

}
