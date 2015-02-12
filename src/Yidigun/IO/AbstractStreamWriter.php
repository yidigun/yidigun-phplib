<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun\IO;

/**
 * Yidigun\IO\AbstractStreamWriter
 * AbstractStreamWriter class
 */
abstract class AbstractStreamWriter implements Writer {

	protected $handle;
	
	public function __destruct() {
		$this->close();
	}

	public function write($str) {
		if ($this->handle) {
			fputs($this->handle, $str);
		}
	}

}
