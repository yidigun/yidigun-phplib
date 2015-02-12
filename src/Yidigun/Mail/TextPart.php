<?php

namespace Yidigun\Mail;

use Yidigun\IO\Writer;

class TextPart extends Part {

	protected $charset;
	protected $text;

	public function __construct($contentType, $text, $charset = "UTF-8") {
		parent::__construct("{$contentType}; charset={$charset}");
		$this->text = $text;
		$this->charset = $charset;
		$this->setHeader("Content-Transfer-Encoding", "base64");
	}
	
	public function setText($text) {
		$this->text = $text;
	}

	public function getText() {
		return $this->text;
	}

	/*
	 * write template methods
	 */
	public function writeBody(Writer &$writer) {
		$writer->write(chunk_split(base64_encode($this->text), 76, "\r\n"));
	}

}
