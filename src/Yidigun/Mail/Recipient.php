<?php
namespace Yidigun\Mail;

class Recipient {

	private $name;
	private $addr;

	public function __construct($addr, $name = null) {
		$this->addr = $addr;
		$this->name = $name;
	}

	public static function newRecipient($rcpt, $name = null) {
		if ($rcpt instanceof Recipient)
			return $rcpt;
		else
			return new Recipient($rcpt, $name);
	}

	public function __toString() {
		$s = "";
		if ($this->name)
			$s .= "\"{$this->name}\" ";
		$s .= "<" . $this->addr . ">";
		return $s;
	}

	public function getName() {
		return $this->name;
	}
	
	public function setName($name) {
		$this->name = $name;
	}

	public function getAddr() {
		return $this->addr;
	}
	
	public function setAddr($addr) {
		$this->addr = $addr;
	}

	public function encode($envelop = false) {
		if ($envelop)
			return "<" . $this->addr . ">";
		else {
			$s = "";
			if ($this->name)
				$s .= "\"" . mb_encode_mimeheader($this->name, "UTF-8", "B") . "\" ";
			$s .= "<" . $this->addr . ">";
			return $s;
		}
	}

}