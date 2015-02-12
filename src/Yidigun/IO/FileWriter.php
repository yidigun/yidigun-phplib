<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun\IO;

/**
 * Yidigun\IO\FileWriter
 * FileWriter class
 */
class FileWriter extends AbstractStreamWriter {

	public function __construct($file, $mode = "w", $context = null) {
		if (is_resource($file)) {
			$this->handle = $file;
		}
		else {
			$this->handle = fopen($file, $mode, $context);
			if (!$this->handle)
				throw new Exception("can't open file: {$file}");
		}
	}

	public function __destruct() {
		$this->close();
	}

	public function close() {
		if ($this->handle) {
			@fclose($this->handle);
			$this->handle = null;
		}
	}

}
