<?php
require_once(__DIR__ . "/lib/config.php");

use Yidigun\JS;
use Yidigun\Format;
use Yidigun\URL;

$manager = new YidigunExampleManager();
//$manager->setDebug(true);

$no = $_REQUEST['no'];
if (!$no) {
	JS::error('Item number is not specified.');
}

try {
	$row = $manager->getRow($no);
}
catch (Exception $e) {
	JS::error('DB error: ' . $e->getMessage());
}

$params = array(
	'qtype'		=> $_REQUEST['qtype'],
	'query'		=> $_REQUEST['query'],
	'qsex'		=> $_REQUEST['qsex'],
	'pageNo'	=> $_REQUEST['pageNo'],
);

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Yidigun PHPLib Examples</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" />
<!--[if lt IE 9]>
<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<link rel="stylesheet" href="css/hardcore.css" />
</head>
<body>
<?php include("inc/nav.php"); ?>

<div class="container">
	<header>
		<h1>Example List</h1>
	</header>

	<div class="view-section">

		<dl>
			<dt>ID</dt>
			<dd colspan="3"><?= $row['no'] ?></dd>

			<dt>Name</dt>
			<dd><?= $row['name'] ?></dd>

			<dt>Company</dt>
			<dd><?= $row['company'] ?></dd>

			<dt>E-Mail</dt>
			<dd><?= Format::link(array('mailto', $row['email'])) ?></dd>

			<dt>Phone No.</dt>
			<dd><?= Format::link(array('tel', $row['phone'])) ?></dd>

			<dt>Sex</dt>
			<dd colspan="3"><?= Format::code($row['sex'], YidigunExample::$SEX, 'Unknown') ?></dd>

			<dt>Create Date</dt>
			<dd><?= Format::date($row['reg_date'], 'Y-m-d H:i:s') ?></dd>

			<dt>Update Date</dt>
			<dd><?= Format::date($row['mod_date'], 'Y-m-d H:i:s') ?></dd>
		</dl>

	</div>

	<div class="buttons">
		<a href="<?= URL::build("example_list.php", $params) ?>" class="btn btn-sm btn-success">
			<span class="glyphicon glyphicon-list" aria-hidden="true"></span> <span>List</span>
		</a>
		<a href="<?= URL::build("example_form.php?mode=W&no={$row['no']}", $params) ?>" class="btn btn-sm btn-primary">
			<span class="glyphicon glyphicon-duplicate" aria-hidden="true"></span> <span>Duplicate</span>
		</a>
		<a href="<?= URL::build("example_form.php?mode=M&no={$row['no']}", $params) ?>" class="btn btn-sm btn-warning">
			<span class="glyphicon glyphicon-edit" aria-hidden="true"></span> <span>Modify</span>
		</a>
		<a href="<?= URL::build("example_proc.php?mode=D&no={$row['no']}", $params) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
			<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Delete</span>
		</a>
	</div>
</div>

<script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>
<script>

$(document).ready(function(event){

});

</script>
</body>
</html>
