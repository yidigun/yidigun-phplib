<?php
require_once(__DIR__ . "/lib/config.php");

use Yidigun\JS;
use Yidigun\Format;
use Yidigun\Form;
use Yidigun\URL;

$manager = new YidigunExampleManager();
//$manager->setDebug(true);

$mode = $_REQUEST['mode'];
if ($mode != 'M' && $mode != 'W') {
	JS::error("Unknown mode: {$mode}");
}

$no = $_REQUEST['no'];
if ($mode == 'M' && !$no) {
	JS::error('Item number is not specified.');
}

if ($no) {
	try {
		$row = $manager->getRow($no);
	}
	catch (Exception $e) {
		JS::error('DB error: ' . $e->getMessage());
	}
}
else {
	$row = array(
		// default values
	);
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

<div class="container list-section">
	<header>
		<h1>Example List</h1>
	</header>

	<div class="form-section">
		<form id="example" name="example" action="example_proc.php" method="post">
			<input type="hidden" name="mode" value="<?= $mode ?>" />

			<div class="form-group">
				<label>ID</label>
				<label class="form-control"><?= ($mode == 'M')? $no: "(Unknown)" ?></label>
				<input type="hidden" name="no" value="<?= $no ?>" />
			</div>

			<div class="form-group">
				<label for="example.name">Name</label>
				<input type="text" name="name" value="<?= $row['name'] ?>" id="example.name" class="form-control" />
			</div>

			<div class="form-group">
				<label for="example.company">Company</label>
				<input type="text" name="company" value="<?= $row['company'] ?>" id="example.company" class="form-control" />
			</div>

			<div class="form-group">
				<label for="example.email">E-Mail</label>
				<input type="text" name="email" value="<?= $row['email'] ?>" id="example.email" class="form-control" />
			</div>

			<div class="form-group">
				<label for="example.phone">Phone No.</label>
				<input type="text" name="phone" value="<?= $row['phone'] ?>" id="example.phone" class="form-control" />
			</div>


			<div class="form-group">
				<label>Sex</label>
				<div class="form-control">
					<div class="radio-inline">
						<label>
							<input type="radio" name="sex" value="M" <?= Form::checked('M', $row['sex']) ?> /> Male
						</label>
					</div>
					<div class="radio-inline">
						<label>
							<input type="radio" name="sex" value="F" <?= Form::checked('F', $row['sex']) ?> /> Female
						</label>
					</div>
				</div>
			</div>

			<div class="buttons">
				<button type="submit" class="btn btn-primary">
					<span class="glyphicon glyphicon-ok" aria-hidden="true"></span> <span>Save</span>
				</button>
				<button type="button" class="btn" onclick="history.back();">
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span> <span>Cancel</span>
				</button>
			</div>
		</form>
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
