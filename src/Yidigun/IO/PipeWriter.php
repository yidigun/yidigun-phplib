<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun\IO;

/**
 * Yidigun\IO\PipeWriter
 * PipeWriter class
 */
class PipeWriter extends AbstractStreamWriter {

	private $status = 0;

	public function __construct($command, $mode = "w") {
		if (is_resource($command)) {
			$this->handle = $command;
		}
		else {
			$this->handle = popen($command, $mode);
			if (!$this->handle)
				throw new Exception("can't open file: {$file}");
		}
	}

	public function getStatus() {
		return $this->status;
	}

	public function close() {
		if ($this->handle) {
			$this->status = @pclode($this->handle);
			$this->handle = null;
		}
	}

}
