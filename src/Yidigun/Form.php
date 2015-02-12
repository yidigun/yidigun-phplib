<?php

namespace Yidigun;

class Form {

	public static function selected($value, $current) {
		return ($value == $current)? " selected": "";
	}

	public static function checked($value, $current) {
		return ($value == $current)? " checked": "";
	}

	public static $DEFAULT_ALLOWED = array('gif', 'jpg', 'png');

	public static function isAllowedFileType(array $file, array $allowed = null) {

		if ($allowed == null)
			$allowed = self::$DEFAULT_ALLOWED;

		if (strpos($file['name'], '.') === FALSE) {
			return false;
		}
		else {
			$extension = strtolower(preg_replace('/^.*\./i', '', $filename));
			return in_array($extension, $allowed);
		}
	}

	public static function saveFile(array &$file, $basedir, $dir = null) {

		if ($file['error'] != UPLOAD_ERR_OK)
			return;

		$savedir = ($dir)? ($basedir . DIRECTORY_SEPARATOR . $dir): $basedir;

		$filename = basename($file['name']);

		if (strpos($filename, '.') === FALSE) {
			$basename = $filename;
			$extension = "";
		}
		else {
			$basename = preg_replace('/\.[a-z0-9]+$/i', '', $filename);
			$extension = strtolower(preg_replace('/^.*\./i', '', $filename));
		}

		$savefile = $filename;

		$i = 1;
		while (file_exists($savedir . DIRECTORY_SEPARATOR . $savefile)) {
			$savefile = $basename . "[" . (++$i) . "]" . (($extension)? ".{$extension}": '');
		}

		umask(002);
		$rs = move_uploaded_file($file['tmp_name'], $savedir . DIRECTORY_SEPARATOR . $savefile);
		if ($rs) {
			$file['savefile'] = $savefile;
			return $savefile;
		}
		else {
			throw new Exception("can't save file: " . $savedir . DIRECTORY_SEPARATOR . $savefile);
		}
	}

	public static function hidden(array $params, $excludes = array()) {
		foreach ($params as $name => $value) {
			if (
				(is_array($excludes) && in_array($name, $excludes)) ||
				($excludes && $name == $excludes)
			) {
				continue;
			}
			echo '<input type="hidden" name="' . htmlspecialchars($name) .
					 '" value="' . htmlspecialchars($value) . '" />';
		}
	}

	public static function redirect($url, array $params, $excludes = array()) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<form id="redirect" name="redirect" method="post" action="<?= $url ?>">
<?php self::hidden($params, $excludes); ?>
</form>
<script type="text/javascript">
var f = document.getElementById('redirect');
f.submit();
</script>
</body>
</html>
<?php
		exit();
	}
}

