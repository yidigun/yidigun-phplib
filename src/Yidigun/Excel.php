<?php

namespace Yidigun;

use Yidigun\IO\StdoutWriter;
use Yidigun\IO\Writer;
use PDO;

class Excel {

	const TEXT		= 't';
	const NUMBER	= 'n';
	const FLOAT		= 'f';
	const PERCENT	= 'p';
	const EMAIL		= 'e';
	const URL		= 'u';
	const CODE		= 'c';
	const DATE		= 'd';
	const DATETIME	= 'dt';

	const INDEX		= "__INDEX__";
	const RINDEX	= "__REVERSED_INDEX__";

	protected $filter;

	protected $title;
	protected $filename;
	protected $columns = array();

	protected $timestamp = 0;
	protected $count = 0;
	protected $statement = null;

	public function __construct(WebFilter $filter) {
		$this->filter = $filter;
		$this->timestamp = time();
	}

	/*
	 * getters & setters
	 */

	public function __get($name) {
		if ($name == 'filename')
			return $this->getFileName();
		elseif ($name == 'title')
			return $this->getTitle();
		elseif ($name == 'count' || $name == 'statement')
			return $this->$name;
	}

	public function __set($name, $value) {
		if ($name == 'title' || $name == 'filename' || $name == 'count' || $name == 'statement')
			$this->$name = $value;
	}

	public function getFilename() {
		if ($this->filename == null) {
			$filename = preg_replace('![\s:/\\\\]+!', '_', $this->title);
			if ($this->filter->filters)
				$filename .= "-{$this->filter->filterString}";
			$filename .= "-" . date("Ymd_His", $this->timestamp) . ".xls";
			$this->filename = $filename;
		}
		return $this->filename;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
	}

	public function getTitle() {
		return ($this->filter->filters)?
			"{$this->title} ({$this->filter->filterString})":
			$this->title;
	}

	public function setTitle($title) {
		$this->title = $title;
	}

	public function getFilter() {
		return $this->count;
	}

	public function setFilter($filter) {
		$this->filter = $filter;
	}

	public function getCount() {
		return $this->count;
	}

	public function setCount($count) {
		$this->count = $count;
	}

	public function getStatement() {
		return $this->statement;
	}

	public function setStatement(PDOStatement $statement) {
		$this->statement = $statement;
	}

	/*
	 * column management
	 */

	public function column($name, $displayName = null, $type = self::TEXT, $defaultValue = '') {
		$this->addColumn($name, $displayName, $type, $defaultValue);
		return $this;
	}

	public function addColumn($name, $displayName = null, $type = self::TEXT, $defaultValue = '') {

		if ($name == self::INDEX) {
			$column = array(
				'name'			=> self::INDEX,
				'displayName'	=> (($displayName)? $displayName: "NO"),
				'type'			=> self::NUMBER,
				'defaultValue'	=> $defaultValue,
			);
		}
		elseif (is_array($type)) {
			$column = array(
				'name'			=> $name,
				'displayName'	=> (($displayName)? $displayName: $name),
				'type'			=> self::CODE,
				'codes'			=> $type,
				'defaultValue'	=> $defaultValue,
			);
		}
		else {
			$column = array(
				'name'			=> $name,
				'displayName'	=> (($displayName)? $displayName: $name),
				'type'			=> $type,
				'defaultValue'	=> $defaultValue,
			);
		}
		
		$this->columns[] = $column;
	}

	/*
	 * write
	 */

	public function header() {
		Header("Content-Type: application/vnd.ms-excel");
		HTTP::contentDisposition("attachment", $this->getFilename());
	}

	public function write() {
		$stdout = new StdoutWriter();
		$this->writeTo($stdout);
	}

	public function writeTo(Writer &$writer) {

		$this->writeHeader($writer);
		$this->writeTable($writer);
		$this->writeFooter($writer);
	}

	public function writeTable(Writer &$writer) {

		// meta info
		$writer->write('<table class="metainfo"><tr><th>Count</th>');
		$this->writeTableData($writer, self::NUMBER, $this->count);
		$writer->write('</tr><tr><th>Date</th>');
		$this->writeTableData($writer, self::DATETIME, date('Y-m-d H:i:s', $this->timestamp));
		$writer->write('</tr></table><br />');

		// thead
		$writer->write('<table><thead><tr>');

		foreach ($this->columns as $column) {
			$writer->write("<th>" . $column['displayName'] . "</th>");
		}

		// tbody
		$writer->write('</tr></thead><tbody>');

		$i = 0;
		while ($row = $this->statement->fetch(PDO::FETCH_ASSOC)) {

			$writer->write("<tr>");
			foreach ($this->columns as $column) {

				$name = $column['name'];
				$value = $row[$name];
				if ($column['name'] == self::INDEX) {
					$this->writeTableData($writer, self::NUMBER, ($i + 1));
				}
				elseif ($column['name'] == self::RINDEX) {
					$this->writeTableData($writer, self::NUMBER, ($this->count - $i));
				}
				elseif ($column['type'] == self::CODE) {
					$value = $column['codes'][$value];
					$this->writeTableData($writer, self::TEXT, (($value)? $value: $column['defaultValue']));
				}
				else {
					$this->writeTableData($writer, $column['type'], (($value)? $value: $column['defaultValue']));
				}
			}
			$writer->write("</tr>");
			$i++;
		}
		$writer->write('</tbody></table>');
	}

	public function writeTableData(Writer $writer, $type, $value) {

		switch ($type) {
		case 'n':
		case 'nl':
		case 'nr':
		case 'nc':
			$formatted = Format::number($value);
			$writer->write("<td class=\"{$type}\" x:num=\"{$value}\">{$formatted}</td>");
			break;
		case 'f':
		case 'fl':
		case 'fr':
		case 'fc':
			$formatted = Format::number($value, 2);
			$writer->write("<td class=\"{$type}\" x:num=\"{$value}\">{$formatted}</td>");
			break;
		case 'p':
		case 'pl':
		case 'pr':
		case 'pc':
			$formatted = Format::number(($value * 100), 2) . "%";
			$writer->write("<td class=\"{$type}\" x:num=\"{$value}\">{$formatted}</td>");
			break;

		case self::EMAIL:
			$writer->write("<td><a href=\"mailto:{$value}\">{$value}</a></td>");
			break;
		case self::URL:
			$writer->write("<td><a href=\"{$value}\" target=\"_blank\">{$value}</a></td>");
			break;

		default:
			$formatted = Format::text($value);
			$writer->write("<td class=\"{$type}\">{$formatted}</td>");
		}
	}

	public function writeHeader(Writer &$writer) {

		$title = $this->getTitle();

		$header = <<<EOF
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns:o="urn:schemas-microsoft-com:office:office"
	xmlns:x="urn:schemas-microsoft-com:office:excel"
	xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="ProgId" content="Excel.Sheet">
<title>{$title}</title>
<style type="text/css" media="all">
/* default excel style */
body,th,td,h1,p,a{font-size:11pt;font-family:"맑은 고딕",monospace;}
h1{font-size:16pt;mso-ignore:colspan;margin-top:0;}
p.a{font-size:12pt;mso-ignore:colspan;margin-bottom:0;}
p.m{mso-ignore:colspan;margin:0;}

table{mso-displayed-decimal-separator:"\.";mso-displayed-thousand-separator:"\,";border-collapse:collapse;border:0.5pt solid black;}
tr{mso-height-source:auto;}
col{mso-width-source:auto;}
th,td{border:0.5pt solid black;padding:1px;vertical-align:middle;}
th{text-align:center;font-weight:bold;}
br{mso-data-placement:same-cell;}

/* data format */
.t,.tl,.tr,.tc{mso-number-format:"\@";}					/* Text */
.n,.nl,.nr,.nc{mso-number-format:"\#\,\#\#0_ ";}		/* Number */
.f,.fl,.fr,.fc{mso-number-format:"\#\,\#\#0\.00_ ";}	/* Float */
.p,.pl,.pr,.pc{mso-number-format:"0\.0%";}				/* Percent */
.d,.dl,.dr,.dc{mso-number-format:"yyyy\/mm\/dd";}		/* Date */
.dt,.dtl,.dtr,.dtc{mso-number-format:"yyyy\/mm\/dd\\ hh\:mm\:ss";} /* Date & Time */

/* alignment */
.r,.tr,.n,.nr,.f,.fr,.p,.pr,.dr,.dtr,.er,.ur{text-align:right !important;}
.l,.t,.tl,.nl,.fl,.pl,.dl,.dtl,.el,.u,.ul{text-align:left !important;}
.c,.tc,.nc,.fc,.pc,.d,.dc,.dt,.dtc,.e,.ec,.uc{text-align:center !important;}

/* color theme */
table,td,th{border-color:#9bc2e6;}
th{color:white;background:#5b9bd5;mso-pattern:#5b9bd5 none}
tbody tr.o td{background:#ddebf7;}
tbody tr.v td{background:white;}

/* print */
@page{size:landscape;margin:.75in .25in .75in .25in;mso-header-margin:.3in;mso-footer-margin:.3in;mso-page-orientation:landscape;mso-horizontal-page-align:center;}
</style>
</head>

<body>

<h1>{$title}</h1>
EOF;
		$writer->write($header);
	}

	public function writeFooter(Writer &$writer) {

		$footer = <<<EOF
</body>
</html>
EOF;
		$writer->write($footer);
	}
}
