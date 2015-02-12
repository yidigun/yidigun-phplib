<?php

namespace Yidigun\DB\DataSources;

use Yidigun\DB\DataSource;
use Yidigun\URL;

class mysql extends DataSource {

	protected function __construct($scheme) {
		parent::__construct($scheme);
	}

	protected function initFromURL($urlInfo) {
		parse_str($urlInfo['query'], $query);
		if ($urlInfo['host']) {
			$this->username = $urlInfo['user'];
			$this->password = $urlInfo['pass'];
			$this->dsInfo['host'] = $urlInfo['host'];
			$this->dsInfo['port'] = $urlInfo['port'];
			$this->dsInfo['dbname'] = preg_replace('!^/!', '', $urlInfo['path']);
		}
		else {
			$this->dsInfo['unix_socket'] = $urlInfo['path'];
			$this->dsInfo['dbname'] = urldecode($query['dbname']);
		}
		$this->dsInfo['charset'] = urldecode($query['charset']);
	}

	public function toURL() {
		$urlInfo = array(
			'scheme'	=> $this->scheme,
			'user'		=> $this->username,
			'pass'		=> $this->password,
			'query'		=> array(),
		);

		$query = array();
		if ($this->dsInfo['unix_socket']) {
			$urlInfo['path'] = $this->dsInfo['unix_socket'];
			$urlInfo['query']['dbname'] = $this->dsInfo['dbname'];
		}
		else {
			$urlInfo['host'] = $this->dsInfo['host'];
			$urlInfo['port'] = $this->dsInfo['port'];
			$urlInfo['path'] = "/" . $this->dsInfo['dbname'];
		}
		$urlInfo['query']['charset'] = $this->dsInfo['charset'];

		return URL::make($urlInfo);
	}

}
