<?php
require_once(__DIR__ . "/lib/config.php");

use Yidigun\HTTP;
use Yidigun\JS;
use Yidigun\URL;
use Yidigun\WebSearch;
use Yidigun\Excel;

$manager = new YidigunExampleManager();
//$manager->setDebug(true);

$mode = $_REQUEST['mode'];

$params = array(
	'qtype'		=> $_REQUEST['qtype'],
	'query'		=> $_REQUEST['query'],
	'qsex'		=> $_REQUEST['qsex'],
	'pageNo'	=> $_REQUEST['pageNo'],
);

if ($mode == 'X') { // download excel

	$webSearch = new WebSearch();
	$webSearch->pageSize = 10;
	$webSearch->pageNo = $_REQUEST['pageNo'];
	$webSearch->filter($_REQUEST['qtype'], $_REQUEST['query']);
	$webSearch->filter('sex', $_REQUEST['sex']);

	$excel = new Excel($webSearch);

	$excel->title = "Example List";

	$excel->column(Excel::INDEX)
		->column('no', 'ID', Excel::NUMBER)
		->column('name', 'Name', Excel::TEXT)
		->column('company', 'Company', Excel::TEXT)
		->column('email', 'E-Mail', Excel::EMAIL)
		->column('sex', 'Sex', YidigunExample::$SEX, 'Unknown')
		->column('reg_date', 'Create Date', Excel::DATETIME)
		->column('mod_date', 'Update Date', Excel::DATETIME);

	try {
		$excel->count = $manager->getCount($webSearch);
		$excel->statement = $manager->getListAll($webSearch);
	}
	catch (Exception $e) {
		JS::error('DB error: ' . $e->geMessage());
	}

	// write and exit
	$excel->header();
	$excel->write();
}
elseif ($mode == 'W') { // insert

	$name		= $_REQUEST['name'];
	$company	= $_REQUEST['company'];
	$email		= $_REQUEST['email'];
	$phone		= $_REQUEST['phone'];
	$sex		= $_REQUEST['sex'];

	$array = array(
		'name'		=> $name,
		'company'	=> $company,
		'email'		=> $email,
		'phone'		=> $phone,
		'sex'		=> $sex,
	);

	try {
		$no = $manager->insertRow($array);

		HTTP::redirect(URL::build("example_view.php?no={$no}", $params));
	}
	catch (Exception $e) {
		JS::error('DB error: ' . $e->geMessage());
	}
}
elseif ($mode == 'M') { // update

	$no = $_REQUEST['no'];
	if (!$no) {
		JS::error('Item number is not specified.');
	}

	$name		= $_REQUEST['name'];
	$company	= $_REQUEST['company'];
	$email		= $_REQUEST['email'];
	$phone		= $_REQUEST['phone'];
	$sex		= $_REQUEST['sex'];

	$array = array(
		'name'		=> $name,
		'company'	=> $company,
		'email'		=> $email,
		'phone'		=> $phone,
		'sex'		=> $sex,
	);

	try {
		$rs = $manager->updateRow($no, $array);

		HTTP::redirect(URL::build("example_view.php?no={$no}", $params));
	}
	catch (Exception $e) {
		JS::error('DB error: ' . $e->geMessage());
	}
}
elseif ($mode == 'D') { // delete

	$no = $_REQUEST['no'];
	if (!$no) {
		JS::error('Item number is not specified.');
	}

	try {
		$rs = $manager->deleteRow($no);

		HTTP::redirect(URL::build("example_list.php", $params));
	}
	catch (Exception $e) {
		JS::error('DB error: ' . $e->geMessage());
	}
}
else {
	JS::error("Unknown mode: {$mode}");
}
