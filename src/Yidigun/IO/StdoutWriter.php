<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun\IO;

/**
 * Yidigun\IO\StdoutWriter
 * StdoutWriter class
 */
class StdoutWriter implements Writer {

	public function write($str) {
		print $str;
	}

	public function close() {
		// do nothing
	}

}
