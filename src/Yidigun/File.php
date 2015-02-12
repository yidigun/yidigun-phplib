<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun;

/**
 * Yidigun\File
 * File utility functions
 */
class File {

	/**
	 * convert rel path to abs path
	 */
	public static function abs($path, $basePath = null, $sep = null) {
		if ($sep == null)
			$sep = DIRECTORY_SEPARATOR;

		// TODO support Windows (drive letter)

		// already abs path
		if (preg_match('!^' . $sep . '!', $path))
			return $path;

		if ($basePath == null) {
			$basePath = (php_sapi_name() == 'cli')?
							$_SERVER['PWD']:
							dirname($_SERVER['SCRIPT_FILENAME']);
		}

		$apath = $basePath . $sep . $path;
		$ipath = explode($sep, $apath);
		$rpath = array();
		foreach ($ipath as $seg) {
			if ($seg == '' || $seg == '.')
				continue;
			elseif ($seg == '..')
				array_pop($rpath);
			else
				array_push($rpath, $seg);
		}

		$result = $sep . implode($sep, $rpath);
		$lastchar = $apath{mb_strlen($apath) - 1};
		if (($result == '' || $lastchar == '.' || $lastchar == $sep) && $result != $sep)
			$result .= $sep;
		return $result;
	}

}

