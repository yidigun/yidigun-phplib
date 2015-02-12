<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun;

/**
 * Yidigun\JS
 * Javascript utility functions
 */
class JS {

	const ACION_RELOAD = "document.reload();";
	const ACION_CLOSE = "window.close();";
	const ACION_BACK = "history.back();";

	public static function redirect($url, $message = null) {
		$action = 'location.replace("' . self::escape($url) .  '")';
		self::alert($message, $action);
	}

	public static function error($message, $action = self::ACION_BACK) {
		self::alert($message, $action);
	}

	public static function alert($message, $action = JS::ACION_BACK) {
		ob_end_clean();
		Header("Cache-Control: no-cache");

		$scripts = (is_array($action))? $action: array($action);
		if ($message)
			array_unshift($scripts, 'alert("' . self::escape($message) . '");');
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />
</head>
<body>
<script type="text/javascript">
<?php
		if (is_array($scripts))
			echo implode("\n", $scripts);
		else
			echo $scripts;
?>
</script>
</body>
</html>
<?php
		exit();
	}

	public static function confirm($message, $okAction = JS::ACION_BACK, $cancelAction = JS::ACION_BACK) {
		ob_end_clean();
		Header("Cache-Control: no-cache");

		$scripts = array();
		$scripts[] = 'if (confirm("' . self::escape($message) . '")) {';
		$scripts[] = ($okAction)? $okAction: '/*do nothing*/';
		$scripts[] = "} else {";
		$scripts[] = ($cancelAction)? $cancelAction: '/*do nothing*/';
		$scripts[] = "}";

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8" />
</head>
<body>
<script type="text/javascript">
<?php
		if (is_array($scripts))
			echo implode("\n", $scripts);
		else
			echo $scripts;
?>
</script>
</body>
</html>
<?php
		exit();
	}

	public static function escape($str) {
		$json = json_encode($str . "");
		return substr($json, 1, strlen($json) - 2);
	}

/*
	public static function escape($str) {
		$str = str_replace("\\", "\\\\", $str);
		$patterns = array("\"", "'", "\n", "\t", "\b", "\f", "\r");
		$replacements = array("\\\"", "\\'", '\n', "\\t", "\\b", "\\f", "\\r");
		return str_replace($patterns, $replacements, $str);
	}
*/
}
