<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
	'ModularAuth.ModularAuthenticator',
), false);

class ModularAuthenticators implements ArrayAccess {
	public $Controller;
	public $Auth;

	protected $_callbackDisabled = false;

	public function init() {
		
	}

	public function reset($authenticators = array()) {
		ModularAuthUtility::flushObjects();

		foreach (Set::normalize($authenticators) as $name  => $settings) {
			$this->__set($name, $settings);
		}
	}

	public function enable($callback = true) {
		return ModularAuthUtility::enableState($this->_callbackDisabled, $callback);
	}

	public function disable($callback = true) {
		return ModularAuthUtility::disableState($this->_callbackDisabled, $callback);
	}

	public function enabled($callback = true) {
		return ModularAuthUtility::enabled($this->_callbackDisabled, $callback);
	}

	private function disabled($callback = true) {
		return ModularAuthUtility::disabled($this->_callbackDisabled, $callback);
	}

	public function get($name) {
		return $this->__get($name);
	}

	public function append($name, $settings = array()) {
		return $this->__set($name, $settings);
	}

	public function exists($name) {
		return $this->__isset($name);
	}

	public function drop($name) {
		return $this->__unset($name);
	}

	public function offsetGet($offset) {
		return $this->__get($name);
	}

	public function offsetSet($offset, $value) {
		return $this->__set($name, $settings);
	}

	public function offsetExists($offset) {
		return $this->__isset($name);
	}

	public function offsetUnset($offset) {
		return $this->__unset($name);
	}

	public function __get($name) {
		return ModularAuthUtility::getObject($name);
	}

	public function __set($name, $value) {
		try {
			if (is_object($value)) {
				if (!($value instanceof ModularAuthenticator)) {
					throw new ModularAuth_IllegalAuthenticatorObjectException;
				}
				ModularAuthUtility::registerObject($name, $value);
				return $this->__get($name);
			} elseif (!$this->__isset($name)) {
				$Authenticator = ModularAuthUtility::loadAuthenticator($name);
			} else {
				$Authenticator = $this->__get($name);
			}
			$Authenticator->configure($value);
			return $Authenticator;
		} catch (ModularAuth_ObjectNotFoundException $e) {
			throw new ModularAuth_AuthenticatorNotFoundException;
		}
	}

	public function __isset($name) {
		return ModularAuthUtility::isRegistered($name);
	}

	public function __unset($name) {
		return ModularAuthUtility::deleteObject($name);
	}

	public function __call($method, $params) {
		
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
}