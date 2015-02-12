<?php
/**
 * config.php
 * set up include path and autoloader
 * set database config
 * set facebook config
 */

/*
 * Place this file and yidigun-phplib in same folder.
 * /path/to/lib/
 *    config.php
 *    yidigun-phplib/
 *       src/
 *          autoloader.php
 *          Yidigun/
 */

/*
// set autoloader
require_once(__DIR__ . "/yidigun-phplib/src/autoloader.php");

// set include path
push_include_path(__DIR__, array(
	"yidigun-phplib/src",

	// add additional libraries
	//"facebook-php-sdk-v4/src",
));
 */

//$autoload_debug = true;
require_once(__DIR__ . "/../../src/autoloader.php");
push_include_path(__DIR__, array(".", "../../src"));


// Database settings
use Yidigun\DB\ConnectionManager;

ConnectionManager::setDefaultAliases(array(
	"exampledb"	=> "mysql://test:test@localhost/test?charset=utf8",
));

// Facebook settings
/*
use Yidigun\Mashup\Facebook;

$facebook = new Facebook(array(
	'appId'				=> '__YOUR_APP_ID__',
	'appSecret'			=> '__YOUR_APP_SECRET__',
	'clientToken'		=> '__YOUR_CLIENT_TOKEN__',
	
	// og: meta info
	'og:title'			=> 'Yidigun PHPLib Examples',
	'og:title_suffix'	=> ' - Yidigun PHPLib Examples',
	'og:desc'			=> 'Yidigun.com is not a dot-COM company.',
	'og:desc_suffix'	=> ' - Yidigun.com is not a dot-COM company.',
	'og:image'			=> '/img/facebook/share_img.jpg',
));
*/

