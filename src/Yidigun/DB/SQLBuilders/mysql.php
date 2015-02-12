<?php

namespace Yidigun\DB\SQLBuilders;

use Yidigun\DB\SQLBuilder;

class mysql extends SQLBuilder {

	/*
	 * select()
	 * ->from()
	 * ->where()
	 * ->groupBy()
	 * ->having()
	 * ->orderBy()
	 * ->limit()
	 */
	protected function buildSelectQuery() {

		$sql = parent::buildSelectQuery();
		if ($this->parts['limit'])
			$sql .= $this->nl() . "LIMIT " . ((is_array($this->parts['limit']))? implode(', ', $this->parts['limit']): $this->parts['limit']);

		return $sql;
	}

	public function limit($limit1, $limit2 = null) {
		$this->parts['limit'] = $limit1;
		if ($limit2)
			$this->parts['limit'] .= ", " . $limit2;
		return $this;
	}

}
