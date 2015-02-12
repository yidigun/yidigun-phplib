<?php
namespace Yidigun;

use \Iterator;

class WebList implements Iterator, WebFilter {

	private $webSearch;
	private $count = 0;
	private $list = array();

	public function __construct(WebSearch $webSearch) {
		$this->webSearch = $webSearch;
	}

	public function __get($name) {
		if ($name == 'webSearch' || $name == 'count' || $name == 'list')
			return $this->$name;
		elseif ($name == 'pages')
			return $this->getPages();
		elseif ($name == 'pageNo')
			return $this->getPageNo();
		elseif ($name == 'startNo')
			return $this->getStartNo();
		elseif ($name == 'virtualStartNo')
			return $this->getVirtualStartNo();
		elseif ($name == 'virtualNo')
			return $this->getVirtualNo();
		else
			return $this->webSearch->$name;
	}

	public function __set($name, $value) {
		if ($name == 'count' || $name == 'list') {
			$this->$name = $value;
		}
	}

	public function getWebSearch() {
		return $this->webSearch;
	}

	public function getCount() {
		return $this->count;
	}
	
	public function setCount($count) {
		$this->count = intval($count);
	}
	
	public function getList() {
		return $this->list;
	}
	
	public function setList($list) {
		$this->list = $list;
	}

	public function getPages() {
		return ($this->count)? intval(ceil($this->count / $this->getPageSize())): 1;
	}

	/*
	 * implements WebFilter
	 */

	public function getPageNo() {
		if ($this->count === null) {
			return $this->webSearch->getPageNo();
		}
		else {
			$pageNo = $this->webSearch->getPageNo();
			$pages = $this->getPages();

			if ($pageNo > $pages)
				$pageNo = $pages;
			if ($pageNo < 1)
				$pageNo = 1;
			return $pageNo;
		}
	}

	public function getPageSize() {
		return $this->webSearch->getPageSize();
	}

	public function getFilters() {
		return $this->webSearch->getFilters();
	}

	public function filter($name, $query) {
		$this->webSearch->filter($name, $query);
	}

	public function getStartNo() {

		return ($this->count)?
			intval(($this->getPageNo() - 1) * $this->getPageSize()):
			$this->webSearch->getStartNo();
	}

	public function getVirtualStartNo() {
		return intval($this->count - $this->getStartNo());
	}

	/*
	 * implements Iterator
	 * @see http://php.net/manual/en/class.iterator.php
	 */

	private $index = 0;

	/**
	 * Return the current element
	 */
	public function current() {
		return $this->list[$this->index];
	}

	public function key() {
		return $this->getVirtualNo();
	}

	public function next() {
		$this->index ++;
	}

	public function rewind() {
		$this->index = 0;
	}

	public function valid() {
		return isset($this->list[$this->index]);
	}

	public function getVirtualNo() {
		return intval($this->getVirtualStartNo() - $this->index);
	}

	/**
	 * print pagination list
	 *
	 * <ul class="pagination">
	 *   <?php $webList->pagination(3, $_SERVER['PHP_SELF'], $params); ?>
	 * </ul>
	 *
	 * @see http://getbootstrap.com/components/#pagination
	 */
	public function pagination($size, $link, array $params = array(), array $options = array()) {

		$pageNo = $this->getPageNo();
		$pages = $this->getPages();
		$paginations = ceil($pages / $size);
		$pagination = ceil($pageNo / $size);

		$start = ($pagination - 1) * $size + 1;
		if ($start < 1)
			$start = 1;

		$end = $start + $size - 1;
		if ($end > $pages)
			$end = $pages;

		//echo "pageNo: {$pageNo}, pages: {$pages}, paginations: {$paginations}, pagination: {$pagination}, start: {$start}, end: {$end}\n";

		$pageno_var = ($options['pageno_var'])? $options['pageno_var']: "pageNo";

		if ($start == 1) {
			echo "<li class=\"disabled\"><span><span aria-hidden=\"true\">&laquo;</span></span></li>";
		}
		else {
			$p = $start - 1;
			$params[$pageno_var] = $p;
			$page_link = URL::build($link, $params);
			$title = "Page #{$p}";
			echo "<li><a href=\"{$page_link}\" title=\"{$title}\" aria-label=\"Previous\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
		}

		for ($p = $start; $p <= $end; $p++) {
			if ($p == $pageNo) {
				echo "<li class=\"active\"><span>{$p} <span class=\"sr-only\">(current)</span></span></li>";
			}
			else {
				$params[$pageno_var] = $p;
				$page_link = URL::build($link, $params);
				$title = "Page #{$p}";
				echo "<li><a href=\"{$page_link}\" title=\"{$title}\">{$p}</a></li>";
			}
		}

		if ($end == $pages) {
			echo "<li class=\"disabled\"><span><span aria-hidden=\"true\">&raquo;</span></span></li>";
		}
		else {
			$p = $end + 1;
			$params[$pageno_var] = $p;
			$page_link = URL::build($link, $params);
			$title = "Page #{$p}";
			echo "<li><a href=\"{$page_link}\" title=\"{$title}\" aria-label=\"Next\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
		}
	}
}
