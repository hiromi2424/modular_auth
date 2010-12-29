<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
	'ModularAuth.ModularAuthenticator',
), false);

class ModularAuthenticators implements ArrayAccess {
	public $Controller;
	public $Auth;

	protected $_loaded = array();
	protected $_callbackDisabled = false;

	public function init() {
		
	}

	public function reset($authenticators = array()) {
		if ($this->_loaded !== array()) {
			$this->_loaded = array();
		}

		foreach (Set::normalize((array)$authenticators) as $name  => $settings) {
			$this->append($name, $settings);
		}
	}

	public function get($name = null) {
		if ($name === null) {
			return array_keys($this->_loaded);
		}
		return $this->get__($name);
	}

	public function append($name, $settings = array()) {
		try {
			if (isset($this->$name)) {
				$this->get($name)->configure($settings);
			} else {
				$Authenticator = ModularAuthUtility::loadLibrary('Component', $name);
				ModularAuth::registerObjct($name, $Authenticator);
				$Authenticator->configure($settings);
			}
		} catch (ModularAuth_ObjectNotFoundException $e) {
			throw new ModularAuth_AuthenticatorNotFoundException;
		}
	}

	public function enable($callback = true) {
		return ModularAuthUtility::enableState($this->_callbackDisabled, $callback);
	}

	public function disable($callback = true) {
		return ModularAuthUtility::disableState($this->_callbackDisabled, $callback);
	}

	private function __delegate($method, $name, $params, $return = ture) {
		if ($name === null) {
			$name = $this->get();
		}

		if (is_array($name)) {
			$results = array();
			foreach ($name as $n) {
				$results[$n] = $this->__delegate($method, $n, $params);
				if ($return === 'boolean' && !$results[$n]) {
					return false;
				}
			}
			switch ($return) {
				case 'boolean':
					return true;
				case true:
				default:
					return $results;
			}
		}

		if (isset($this->$name)) {
			if (!is_callable(array($this->$name, $method))) {
				throw new ModularAuth_IllegalAuthenticatorMethodException;
			}
			return call_user_func_array(array($this->$name, $method), $params);
		}
	}

	public function drop($name) {
		return $this->__unset($name);
	}

	public function __call($method, $params) {
		
	}

	public function __get($name) {
		if ($this->__isset($name)) {
			return $this->_loaded[ModularAuthUtility::normalize($name)];
		}
		return null;
	}

	public function __set($name, $value) {

		if (is_object($value)) {
			if ($value instanceof ModularAuthenticator) {
			} else {
				throw new ModularAuth_IllegalAuthenticatorObjectException;
			}
		} elseif (is_array($value)) {
			$this->append($name, $value);
		}
	}

	public function __isset($name) {
		return isset($this->_loaded[ModularAuthUtility::normalize($name)]);
	}

	public function __unset($name) {
		if ($this->__isset($name)) {
			unset($this->_loaded[ModularAuthUtility::normalize($name)]);
			return true;
		}
		return false;
	}

	public function offsetExists($offset) {
		return $this->__isset($offset);
	}

	public function offsetGet($offset) {
		return $this->__get($offset);
	}

	public function offsetSet($offset, $value) {
		return $this->__set($offset, $value);
	}

	public function offsetUnset($offset) {
		return $this->__unset($offset);
	}
}