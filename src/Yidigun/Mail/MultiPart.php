<?php

namespace Yidigun\Mail;

use Yidigun\IO\Writer;

class MultiPart extends Part {

	protected $boundary;
	protected $parts = array();

	public function __construct($contentType) {
		$this->boundary = uniqid("--=multipart.", true);
		parent::__construct("{$contentType}; boundary={$this->boundary}");
	}
	
	public function getBoundary() {
		return $this->boundary;
	}

	/*
	 * part management
	 */

	public function addPart($part) {
		$this->parts[] = $part;
	}

	public function getParts() {
		return $this->parts;
	}

	/*
	 * write template methods
	 */
	public function writeBody(Writer &$writer) {
		foreach ($this->parts as $part) {
			if ($part instanceof Part) {
				$writer->write("--");
				$writer->write($this->boundary);
				$writer->write("\r\n");
				$part->write($writer);
			}
		}
		$writer->write("--");
		$writer->write($this->boundary);
		$writer->write("--");
		$writer->write("\r\n");
		$writer->write("\r\n");
	}

}
