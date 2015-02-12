<?php

namespace Yidigun\Util;

/**
 * Parameter array wrapper.
 */
class ArrayParams {

	protected $v;

	public function __construct(&$v) {
		$this->v =& $v;
	}

	public function &toArray() {
		return $v;
	}

	public function &__get($name) {
		return $this->ref($name);
	}

	public function __set($name, $value) {
		$this->set($name, $value);
	}

	public function __isset($name) {
		return $this->has($name);
	}

	public function __unset($name) {
		$this->_unset($name);
	}

	public function has($name) {
		return isset($this->v[$name]);
	}

	public function get($name) {
		return ($this->has($name))? $this->v[$name]: null;
	}

	public function &ref($name) {
		if (!$this->has($name))
			$this->v[$name] = null;
		return $this->v[$name];
	}

	public function set($name, $value) {
		$this->v[$name] = $value;
	}
	
	public function setRef($name, &$ref) {
		$this->v[$name] =& $ref;
	}

	public function setIfEmpty($name, $value) {
		if ($this->isEmpty($name))
			$this->v[$name] = $value;
	}
	
	public function setRefIfEmpty($name, &$ref) {
		if ($this->isEmpty($name))
			$this->v[$name] =& $ref;
	}

	public function _unset($name) {
		unset($this->v[$name]);
	}

	public function isEmpty($name) {
		return !$this->has($name) || $this->v[$name] === '';
	}

	public function is_empty($name) {
		return $this->isEmpty($name);
	}

	public function isNumeric($name) {
		return $this->has($name) && is_numeric($this->v[$name]);
	}

	public function is_numeric($name) {
		return $this->isNumeric($name);
	}

	public function isArray($name) {
		return $this->has($name) && is_array($this->v[$name]);
	}

	public function is_array($name) {
		return $this->isArray($name);
	}

	public function isObject($name) {
		return $this->has($name) && is_object($this->v[$name]);
	}

	public function is_object($name) {
		return $this->isObject($name);
	}

	public function int($name, $defaultValue = 0) {
		if ($this->isNumeric($name))
			return (int)$this->v[$name];
		else
			return $defaultValue;
	}

	public function float($name, $defaultValue = 0.0) {
		if ($this->isNumeric($name))
			return (float)$this->v[$name];
		else
			return $defaultValue;
	}

	public function bool($name, $defaultValue = false) {
		if ($this->has($name))
			return (boolean)$this->v[$name];
		else
			return $defaultValue;
	}

	public function flag($name, $defaultValue = false) {
		if ($this->isNumeric($name)) {
			return (boolean)$this->v[$name];
		}
		else if ($this->isArray($name)) {
			return count($this->v[$name]) > 0;
		}
		else if ($this->isObject($name)) {
			return true;
		}
		else if ($this->has($name)) {
			$value = $this->v[$name];
			return !(
					$value == null || $value == '' ||
					strcasecmp('no', $value) == 0 ||
					strcasecmp('n', $value) == 0 ||
					strcasecmp('false', $value) == 0 ||
					strcasecmp('f', $value) == 0
			);
		}
		else {
			return $defaultValue;
		}
	}

	public function str($name, $defaultValue = '', $emptyAsNull = true) {
		if (($emptyAsNull)? !$this->isEmpty($name): $this->has($name))
			return (string)$this->v[$name];
		else
			return $defaultValue;
	}

	public function arr($name) {
		if ($this->has($name))
			return (is_array($this->v[$name]))? $this->v[$name]: array($this->v[$name]);
		else
			return array();
	}
}
