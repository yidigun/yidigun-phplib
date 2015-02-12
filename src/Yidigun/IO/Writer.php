<?php
/**
 * Copyright 2015 Yidigun.com
 */
namespace Yidigun\IO;

/**
 * Yidigun\IO\Writer
 * Writer interface
 */
interface Writer {

	public function write($str);

	public function close();

}
