<?php
/**
 * autoloader.php
 * set up include path and autoloader
 */

function push_include_path($base, $libs = null) {
	// set include_path
	$paths = explode(PATH_SEPARATOR, ini_get('include_path'));

	if (!in_array($base, $paths))
		$paths[] = $base;

	if ($libs) {
		if (is_array($libs)) {
			foreach ($libs as $lib) {
				$paths[] = "{$base}/{$lib}";
			}
		}
		else {
			$paths[] = "{$base}/{$libs}";
		}
	}

	ini_set('include_path', implode(PATH_SEPARATOR, $paths));

	if ($GLOBALS['autoload_debug']) {
		echo "include_path: ";
		print_r($paths);
	}
}

// start autoloader
spl_autoload_register(function($class){

	$paths = explode(PATH_SEPARATOR, ini_get('include_path'));
	$classfile = str_replace("_", DIRECTORY_SEPARATOR,
				str_replace("\\", DIRECTORY_SEPARATOR, $class)) . ".php";

	$classpath = '';
	foreach ($paths as $path) {
		// check name as-is
		if (file_exists($path . DIRECTORY_SEPARATOR . $classfile)) {
			$classpath = $path . DIRECTORY_SEPARATOR . $classfile;
			break;
		}
	// force name to lower case and try again
		elseif (file_exists($path . DIRECTORY_SEPARATOR . strtolower($classfile))) {
			$classpath = $path . DIRECTORY_SEPARATOR . strtolower($classfile);
			break;
		}
	}

//	$elvl = error_reporting(E_ERROR | E_PARSE);
	if ($classpath) {
		include_once($classpath);
	}
//	error_reporting($elvl);

	if ($GLOBALS['autoload_debug']) {
		echo "[autoloader]: {$class} => {$path}";
		echo ", file " . (($classpath)? "exists": "not exists");
		echo ", class " . ((class_exists($class))? "loaded": "not loaded");
		echo ".\n";
	}
});
