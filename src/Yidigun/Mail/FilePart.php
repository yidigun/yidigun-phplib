<?php

namespace Yidigun\Mail;

use Yidigun\IO\Writer;
use Yidigun\MIME;

class FilePart extends Part {

	protected $filename;

	public function __construct($filename, $disposition = null, $contentType = null) {
		if (!file_exists($filename))
			throw new Exception("file not fount: {$filename}");

		$contentType = ($contentType)? $contentType: MIME::fileType($filename);
		parent::__construct($contentType);
		$this->filename = $filename;
		if ($disposition) {
			$basename = mb_encode_mimeheader(basename($filename), "UTF-8", "B");
			$this->setHeader("Content-Disposition", "{$disposition}; filename=\"{$basename}\"");
		}
		$this->setHeader("Content-Transfer-Encoding", "base64");
	}

	public function setFilename($filename) {
		$this->filename = $filename;
	}

	public function getFilename() {
		return $this->filename;
	}

	/*
	 * write template methods
	 */
	public function writeBody(Writer &$writer) {
		$fh = fopen($this->filename, "r");
		while (!feof($fh)) {
			$line = fread($fh, 57);
			$writer->write(base64_encode($line));
			$writer->write("\r\n");
		}
		fclose($fh);
	}

}
