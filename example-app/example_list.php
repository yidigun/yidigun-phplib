<?php
require_once(__DIR__ . "/lib/config.php");

use Yidigun\JS;
use Yidigun\WebSearch;
use Yidigun\Format;
use Yidigun\Form;
use Yidigun\URL;

$manager = new YidigunExampleManager();
//$manager->setDebug(true);

$webSearch = new WebSearch();
$webSearch->pageSize = 10;
$webSearch->pageNo = $_REQUEST['pageNo'];
$webSearch->filter($_REQUEST['qtype'], $_REQUEST['query']);
$webSearch->filter('sex', $_REQUEST['qsex']);

try {
	$webList = $manager->getWebList($webSearch);
}
catch (Exception $e) {
	JS::error('DB error: ' . $e->getMessage());
}

$params = array(
	'qtype'		=> $_REQUEST['qtype'],
	'query'		=> $_REQUEST['query'],
	'qsex'		=> $_REQUEST['qsex'],
	'pageNo'	=> $webList->pageNo,
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

	<div class="list-table">
		<table class="table table-striped table-hover table-condensed">
		<caption>
			<?= ($webList->filters)? "Filter: (" . $webList->filterString . "), ": "" ?>
			Count: <?= number_format($webList->count) ?>,
			Page: <?= number_format($webList->getPageNo()) ?> / <?= number_format($webList->getPages()) ?>,
			Date: <?= strftime("%c") ?>
		</caption>
		<thead>
			<tr>
				<th>NO</th>
				<th>Name</th>
				<th>Company</th>
				<th>E-Mail</th>
				<th>Phone No.</th>
				<th>Sex</th>
				<th>DATE</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if ($webList->count == 0) {
		?>
			<tr>
				<td colspan="7">Not found.</td>
			</tr>
		<?php
			}
			else {
				foreach ($webList as $vno => $row) {
		?>
			<tr>
				<td><?= $vno ?></td>
				<td><a href="<?= URL::build("example_view.php?no={$row['no']}", $params) ?>"><?= $row['name'] ?></a></td>
				<td><?= $row['company'] ?></td>
				<td><?= Format::link(array('mailto', $row['email'])) ?></td>
				<td><?= Format::link(array('tel', $row['phone'])) ?></td>
				<td><?= Format::code($row['sex'], YidigunExample::$SEX, 'Unknown') ?></td>
				<td><?= Format::date($row['mod_date']) ?></td>
			</tr>
		<?php
				}
			}
		?>
		</tbody>
		</table>
	</div>

	<div class="list-pagination">
		<ul class="pagination">
		<?php
			unset($params['pageNo']);
			$webList->pagination(5, $_SERVER['PHP_SELF'], $params);
		?>
		</ul>
	</div>

	<div class="list-search">
		<form action="<?= $_SERVER['PHP_SELF'] ?>" method="get" class="form-inline">
			<p>
				<label>
					<input type="radio" name="qsex" value=""<?= ($_REQUEST['qsex'] != 'M' && $_REQUEST['qsex'] != 'F')? " checked": "" ?> /> All
				</label>
				<label>
					<input type="radio" name="qsex" value="M"<?= Form::checked('M', $_REQUEST['qsex']) ?> /> Male
				</label>
				<label>
					<input type="radio" name="qsex" value="F"<?= Form::checked('F', $_REQUEST['qsex']) ?> /> Female
				</label><br />
				<select name="qtype" class="form-control">
					<option value="name"<?= Form::selected('name', $_REQUEST['qtype']) ?>>Name</option>
					<option value="company"<?= Form::selected('company', $_REQUEST['qtype']) ?>>Company</option>
					<option value="email"<?= Form::selected('email', $_REQUEST['qtype']) ?>>E-Mail</option>
					<option value="phone"<?= Form::selected('phone', $_REQUEST['qtype']) ?>>Phone</option>
				</select>
				<input type="text" name="query" value="<?= $_REQUEST['query'] ?>" class="form-control" />
				<button type="submit" class="btn btn-default">Search</button>
			</p>
		</form>
	</div>

	<div class="buttons">
		<a href="<?= URL::build("example_form.php?mode=W", $params) ?>" class="btn btn-sm btn-primary">
			<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> <span>New</span>
		</a>
		<a href="<?= URL::build("example_proc.php?mode=X", $params) ?>" class="btn btn-sm btn-success">
			<span class="glyphicon glyphicon-download" aria-hidden="true"></span> <span>Download Excel</span>
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
