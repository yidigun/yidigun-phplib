<?php
namespace Yidigun;

use Yidigun\Mail\Message;
use Yidigun\IO\PipeWriter;

class Mail {
	
	public static function sendmail(Message $message, $sendmail = null) {
		if (!$sendmail)
			$sendmail = ini_get('sendmail_path');

		$from_email = $message->getFrom()->getAddr();

		$command = "{$sendmail} -f{$from_email}";

		$sendmail = new PipeWriter($command);
		$message->write($sendmail);
		$sendmail->close();

		return ($sendmail->getStatus() != -1);
	}

}
