<?php
namespace Yidigun\Mail;

use Yidigun\IO\Writer;
use Yidigun\Mail\MultiPart;
use Yidigun\Mail\TextPart;
use Yidigun\Mail\FilePart;
use Yidigun\MIME;

class Message extends MultiPart {

	private $from;
	private $replyTo;
	private $to = array();
	private $cc = array();
	private $bcc = array();
	private $subject;

	private $bodyPart;
	private $attachs = array();

	public function __construct() {
		parent::__construct("multipart/mixed");
	}

	/*
	 * body & attach
	 */

	public function setTextBody($text) {
		$this->bodyPart = new TextPart("text/plain", $text);
	}

	public function setHtmlBody($html) {
		$this->bodyPart = new TextPart("text/html", $html);
	}

	public function setAlternativeBody($text, $html) {
		$this->bodyPart = new MultiPart("multipart/alternative");
		$this->bodyPart->addPart(new TextPart("text/plain", $text));
		$this->bodyPart->addPart(new TextPart("text/html", $html));
	}

	public function addAttach($filename) {
		$this->attachs[] = new FilePart($filename, "attachment");
	}

	/*
	 * getters & setters
	 */

	// Subject:
	public function getSubject() {
		return $this->subject;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	// From:
	public function getFrom() {
		return $this->from;
	}

	public function setFrom($from, $name = null) {
		$this->from = Recipient::newRecipient($from, $name);
	}

	// Reply-To:
	public function getReplyTo() {
		return $this->replyTo;
	}

	public function setReplyTo($replyTo) {
		$this->replyTo = Recipient::newRecipient($replyTo);
	}

	// To:
	public function getTo() {
		return $this->to;
	}

	public function setTo($to, $name = null) {
		$this->to = array();
		$this->addTo($to, $name);
	}

	public function addTo($to, $name = null) {
		if (is_array($to)) {
			foreach ($to as $to1)
				$this->to[] = Recipient::newRecipient($to1);
		}
		else {
			$this->to[] = Recipient::newRecipient($to, $name);
		}
	}

	// Cc:
	public function getCc() {
		return $this->cc;
	}

	public function setCc($cc, $name = null) {
		$this->cc = array();
		$this->addCc($cc, $name);
	}

	public function addCc($cc, $name = null) {
		if (is_array($cc)) {
			foreach ($cc as $cc1)
				$this->cc[] = Recipient::newRecipient($cc1);
		}
		else {
			$this->cc[] = Recipient::newRecipient($cc, $name);
		}
	}

	// Bcc:
	public function getBcc() {
		return $this->bcc;
	}

	public function setBcc($bcc, $name = null) {
		$this->bcc = array();
		$this->addBcc($bcc, $name);
	}

	public function addBcc($bcc, $name = null) {
		if (is_array($bcc)) {
			foreach ($bcc as $bcc1)
				$this->bcc[] = Recipient::newRecipient($bcc1);
		}
		else {
			$this->bcc[] = Recipient::newRecipient($bcc, $name);
		}
	}

	/*
	 * override write()
	 */
	public function write(Writer &$writer) {
		if (!$this->parts) {
			$this->addPart($this->bodyPart);
			foreach ($this->attachs as $attach)
				$this->addPart($attach);
		}
		parent::write($writer);
	}

	public function writeHeaders(Writer &$writer) {
		if ($this->from)
			$writer->write(MIME::header("From", $this->from->encode()) . "\r\n");
		if ($this->replyTo)
			$writer->write(MIME::header("Reply-To", $this->replyTo->encode()) . "\r\n");
		if ($this->to)
			$writer->write(MIME::header("To", implode(", ", array_map(function($r){return $r->encode();}, $this->to))) . "\r\n");
		if ($this->cc)
			$writer->write(MIME::header("Cc", implode(", ", array_map(function($r){return $r->encode();}, $this->cc))) . "\r\n");
		if ($this->bcc)
			$writer->write(MIME::header("Bcc", implode(", ", array_map(function($r){return $r->encode();}, $this->bcc))) . "\r\n");

		if ($this->subject)
			$writer->write(MIME::header("Subject", $this->subject, true) . "\r\n");

		parent::writeHeaders($writer);
	}

	public function writeBody(Writer &$writer) {
		$writer->write("This is a multi-part message in MIME format.\r\n\r\n");
		parent::writeBody($writer);
	}
}