<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun;

/**
 * Yidigun\HTTP
 * HTTP utility functions
 */
class HTTP {

	/**
	 * alias of Server::getRemoteAddr()
	 */
	public static function getRemoteAddr() {
		return Server::getRemoteAddr();
	}
	
	public static function nocache() {
		Header("Cache-Control: no-cache");
		Header("Expires: 0");
		Header("Pragma: no-cache");
	}

	public static function cacheControl($cacheControl) {
		Header("Cache-Control: {$cacheControl}");
	}

	public static function redirect($url, $status = 302) {
		Header("HTTP/1.1 {$status} {$message}");
		Header("Location: {$url}");

		ob_end_clean();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8" />
<meta http-equiv="Refresh" content="0; url=<?= htmlspecialchars($url) ?>" />
</head>
<body>
<p>Page is moved. Please click <a href="<?= htmlspecialchars($url) ?>">here</a>.</p>
</body>
</html>
<?php
		exit();
	}

	public static function error($status, callable $renderer = null) {
		ob_end_clean();
		self::printStatusHeader($status);
		Header("Cache-Control: no-cache");
		if ($renderer == null)
			self::printErrorPage($status);
		else
			call_user_func($renderer, $status);
		exit();
	}

	public static function errorPage($status, $filename) {
		self::error($status, function($status) use ($filename) {
			include($filename);
		});
	}

	public static function errorFile($status, $filename) {
		self::error($status, function($status) use ($filename) {
			HTTP::writeFile($filename, array('contentDisposition' => ''));
		});
	}

	/*
	 * write a file
	 */

	public static function writeFile($filename, array $options = array()) {
		if (!isset($options['contentDisposition']) || !$options['contentDisposition'])
			$disposition = 'attachment';
		else
			$options['contentDisposition'] = $options['contentDisposition'];

		$basename = (isset($options['filename']) && $options['filename'])?
							$options['filename']: basename($filename);

		$mimeType = (isset($options['contentType']))?
						$options['contentType']: mime_content_type($filename);
		if (preg_match('!^text/!', $mimeType)) {
			$charset = (isset($options['charset']) && $options['charset'])? $options['charset']: ((ini_get('default_charset'))? ini_get('default_charset'): 'UTF-8');
			$mimeType .= "; charset=\"{$charset}\"";
		}

		$useIncludePath = (isset($options['useIncludePath']))? $options['useIncludePath']: false;
		$fh = (isset($options['context']) && $options['context'])?
						fopen($filename, "r", $useIncludePath, $options['context']):
						fopen($filename, "r", $useIncludePath);
		if (!$fh)
			throw new Exception("file not found: {$filename}");

		$fileInfo = fstat($fh);

		ob_end_clean();
		Header("Content-Type: {$mimeType}");
		if ($disposition != 'none')
			self::contentDisposition($disposition, $basename);
		Header("Content-Length: {$fileInfo['size']}");
		fpassthru($fh);
		exit();
	}

	public static function contentDisposition($disposition, $filename = null) {
		$header = "Content-Disposition: {$disposition}";
		if ($filename) {
			if (UserAgent::isIE() && UserAgent::getVersion() <= 8) {
				$header .= "; filename=" . urlencode($filename);
			}
			else if (UserAgent::isSafari()) {
				$header .= "; filename=\"" . $filename . "\"";
			}
			else {
				$header .= "; filename*=UTF-8''" . urlencode($filename);
			}
		}
		Header($header);
	}

	const HEX_BLANK_GIF = "47494638396101000100910000000000ffffffffffff00000021f90401000002002c00000000010001000002025401003b";

	public static function writeBlankGif() {
		$bin = hex2bin(self::HEX_BLANK_GIF);
		Header("Content-Type: image/gif");
		Header("Content-Length: " . strlen($bin));
		print($bin);
		exit();
	}

	/*
	 * generate error page
	 */

	public static function printErrorPage($status) {
		$msg = self::getStatusMsg($status);
		$title = "HTTP {$status} {$msg}";
		echo "<!DOCTYPE html>";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?= (ini_get('default_charset'))? ini_get('default_charset'): 'UTF-8' ?>" />
<title><?= htmlspecialchars($title) ?></title>
</head>
<body>
<h1><?= htmlspecialchars($title) ?></h1>
<?php
		$detail = self::getStatusDetail($status);
		if ($detail) {
?>
<p><?= htmlspecialchars($detail) ?></p>
<?php
		}
?>
</body>
</html>
<?php
	}

	public static function printStatusHeader($status) {
		$statusLine = "HTTP/1.1 {$status} " . self::getStatusMsg($status);
		Header($statusLine);
		return $statusLine;
	}

	public static function getStatusInfo($status) {
		return (isset(self::$STATUS_INFO[$status]))?
					self::$STATUS_INFO[$status]: null;
	}

	public static function getStatusMsg($status) {
		$statusInfo = self::getStatusInfo($status);
		return ($statusInfo)? $statusInfo[0]: null;
	}
	
	public static function getStatusDetail($status, $server_vars = null) {
		$REQUEST_METHOD = ($server_vars != null &&
					isset($server_vars['REQUEST_METHOD']))?
						$server_vars['REQUEST_METHOD']: $_SERVER['REQUEST_METHOD'];
		$statusInfo = self::getStatusInfo($status);
		if ($statusInfo) {
			$detail = ($statusInfo)? $statusInfo[1]: null;
			eval('$d2 = "' . $detail . '";');
			return $d2;
		}
		else {
			return null;
		}
	}

	public static $STATUS_INFO = array(

		// Informational 1xx
		100 => array('Continue', ''),
		101 => array('Switching Protocols', ''),

		// Successful 2xx
		200 => array('OK', ''),
		201 => array('Created', ''),
		202 => array('Accepted', ''),
		203 => array('Non-Authoritative Information', ''),
		204 => array('No Content', ''),
		205 => array('Reset Content', ''),
		206 => array('Partial Content', ''),

		// Redirection 3xx
		300 => array('Multiple Choices', ''),
		301 => array('Moved Permanently', ''),
		302 => array('Found', ''),
		303 => array('See Other', ''),
		304 => array('Not Modified', ''),
		305 => array('Use Proxy', ''),
		307 => array('Temporary Redirect', ''),

		// Client Error 4xx
		400 => array('Bad Request', 'Your browser (or proxy) sent a request that this server could not understand.'),
		401 => array('Unauthorized', 'This server could not verify that you are authorized to access the URL. You either supplied the wrong credentials (e.g., bad password), or your browser doesn\'t understand how to supply the credentials required.'),
		402 => array('Payment Required', ''),
		403 => array('Forbidden', 'You don\'t have permission to access the requested object. It is either read-protected or not readable by the server.'),
		404 => array('Not Found', 'The requested URL was not found on this server.'),
		405 => array(
			'Method Not Allowed',
			'The {$REQUEST_METHOD} method is not allowed for the requested URL.'
		),
		406 => array('Not Acceptable', ''),
		407 => array('Proxy Authentication Required', ''),
		408 => array('Request Timeout', 'The server closed the network connection because the browser didn\'t finish the request within the specified time.'),
		409 => array('Conflict', ''),
		410 => array('Gone', 'The requested URL is no longer available on this server and there is no forwarding address.'),
		411 => array('Length Required', 'A request with the {$REQUEST_METHOD} method requires a valid <code>Content-Length</code> header.'),
		412 => array('Precondition Failed', 'The precondition on the request for the URL failed positive evaluation.'),
		413 => array('Request Entity Too Large', 'The {$REQUEST_METHOD} method does not allow the data transmitted, or the data volume exceeds the capacity limit.'),
		414 => array('Request-URI Too Long', 'The length of the requested URL exceeds the capacity limit for this server. The request cannot be processed.'),
		415 => array('Unsupported Media Type', 'The server does not support the media type transmitted in the request.'),
		416 => array('Requested Range Not Satisfiable', ''),
		417 => array('Expectation Failed', ''),

		// Server Error 5xx
		500 => array('Internal Server Error', 'The server encountered an internal error and was unable to complete your request.'),
		501 => array('Not Implemented', 'The server does not support the action requested by the browser.'),
		502 => array('Bad Gateway', 'The proxy server received an invalid response from an upstream server.'),
		503 => array('Service Unavailable', 'The server is temporarily unable to service your request due to maintenance downtime or capacity problems. Please try again later.'),
		504 => array('Gateway Timeout', ''),
		505 => array('HTTP Version Not Supported', ''),

	);

	public static function getPreferedLocale(array $supported, $accept = null) {
		if ($accept == null)
			$accept = $_SERVER['HTTP_ACCEPT_LANGUAGE'];

		$accept_langs = preg_split('/\s*,\s*/', $accept);
		// search locale match
		foreach ($accept_langs as $lang) {
			$lang = str_replace('-', '_', preg_replace('/;.*$/', '', $lang));
			if (in_array($lang, $supported))
				return $lang;
		}

		// search only language match
		foreach ($accept_langs as $lang) {
			$lang = preg_replace('/[-_].*$/', $lang);
			if (in_array($lang, $supported))
				return $lang;
		}
		
		// return default locale
		return $supported[0];
	}

}
