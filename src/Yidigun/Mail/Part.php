<?php

namespace Yidigun\Mail;

use Yidigun\IO\Writer;
use Yidigun\MIME;

abstract class Part {

	protected $contentType;
	protected $headers = array();

	public static $MIME_ENCODE_PREF = array(
		"scheme"			=> "B",
		"input-charset"		=> "UTF-8",
		"output-charset"	=> "UTF-8",
		"line-length"		=> 76,
		"line-break-chars"	=> "\r\n",
	);

	public function __construct($contentType) {
		$this->contentType = $contentType;
	}

	/*
	 * header management
	 */
	public function setHeader($name, $value) {
		$this->headers[$name] = array($value);
	}

	public function addHeader($name, $value) {
		if (!isset($this->headers[$name]))
			$this->headers[$name] = array($value);
		else
			$this->headers[] = $value;
	}

	public function getHeader($name) {
		if (is_array($this->headers[$name]) && count($this->headers[$name]) > 1)
			return $this->headers[$name];
		else
			return $this->headers[$name][0];
	}

	public function getHeaderValues($name) {
		return (isset($this->headers[$name]))? $this->headers[$name]: array();
	}
	
	public function getHeaders() {
		return $this->headers;
	}

	/*
	 * write template methods
	 */
	public function write(Writer &$writer) {
		$this->writeHeaders($writer);
		$writer->write("\r\n");
		$this->writeBody($writer);
	}

	public function writeHeaders(Writer &$writer) {
		$writer->write(MIME::header("Content-Type", $this->contentType));
		$writer->write("\r\n");
		foreach ($this->headers as $name => $values) {
			foreach ($values as $value) {
				$writer->write(MIME::header($name, $value));
				$writer->write("\r\n");
			}
		}
	}

	public abstract function writeBody(Writer &$writer);

}
