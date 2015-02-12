<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun\IO;

/**
 * Yidigun\IO\StringWriter
 * StringWriter class
 */
class StringWriter implements Writer {

	private $buf;

	private $closed = false;

	public function __construct($initial = "") {
		$this->buf = $initial;
	}

	public function __toString() {
		return $this->buf;
	}

	public function write($str) {
		if (!$this->closed)
			$this->buf .= $str;
	}

	public function close() {
		$this->closed = true;
	}

}
