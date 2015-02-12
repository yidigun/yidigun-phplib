<?php
/**
 * autoload.php
 * Setup classpath and autoloader
 */

/**
 * Wheather print debug informations when autoloading.
 */
if (!isset($GLOBALS['autoload_debug']))
	$GLOBALS['autoload_debug'] = false;

/**
 * Global array to store classpath.
 */
$GLOBALS['YIDIGUN_AUOTLOAD_PATH'] = array();

/**
 * Setup classpath for autoloader function.
 */
function autoload_classpath($basedir, $dirs = null) {

	$paths = array($basedir);

	if ($dirs) {
		if (is_array($dirs)) {
			foreach ($dirs as $dir)
				$paths[] = (strpos($dir, DIRECTORY_SEPARATOR) === 0)?
							$dir: $basedir . DIRECTORY_SEPARATOR . $dir;
		}
		else {
			$paths[] = (strpos($dirs, DIRECTORY_SEPARATOR) === 0)?
						$dirs: $basedir . DIRECTORY_SEPARATOR . $dirs;
		}
	}

	$GLOBALS['YIDIGUN_AUOTLOAD_PATH'] = $paths;
}

/*
 * Register autoloader function and start autoloading.
 */
spl_autoload_register(function($class){

	$file = str_replace("_", DIRECTORY_SEPARATOR,
				str_replace("\\", DIRECTORY_SEPARATOR, $class)) . ".php";

	$file_exists = false;
	if (is_array($GLOBALS['YIDIGUN_AUOTLOAD_PATH'])) {
		foreach ($GLOBALS['YIDIGUN_AUOTLOAD_PATH'] as $dir) {
			// check name as-is
			$path = $dir . DIRECTORY_SEPARATOR . $file;
			if (file_exists($path)) {
				$file_exists = true;
				include_once($path);
				if (class_exists($class) || interface_exists($class))
					break;
			}
			// force name to lower case and try again
			$path = $dir . DIRECTORY_SEPARATOR . strtolower($file);
			if (file_exists($path)) {
				$file_exists = true;
				include_once($path);
				if (class_exists($class) || interface_exists($class))
					break;
			}
		}
	}

	if ($GLOBALS['autoload_debug']) {
		echo "[autoload] {$class}";
		if ($file_exists)
			echo " => {$path}";
		if (class_exists($class) || interface_exists($class))
			echo ": class loaded.\n";
		else
			echo ": class not found!\n";
	}
});
