<?php
namespace Yidigun;

interface WebFilter {

	public function getPageNo();

	public function getPageSize();

	public function getFilters();

	public function filter($name, $query);

	public function getStartNo();

}
