<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun\IO;

/**
 * Yidigun\IO\NullWriter
 * NullWriter class
 */
class NullWriter implements Writer {

	public function write($str) {
		/* do nothing */
	}

	public function close() {
		/* do nothing */
	}

}
