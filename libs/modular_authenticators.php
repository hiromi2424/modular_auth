<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
	'ModularAuth.ModularAuthenticator',
), false);

class ModularAuthenticators extends ModularAuthBaseObject implements ArrayAccess {

	public function reset($authenticators = array()) {
		ModularAuthUtility::flushObjects();

		foreach (Set::normalize($authenticators) as $name  => $settings) {
			$this->__set($name, $settings);
		}
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

	public function triggerCallback($callback, $method, $params, $return = true) {
		if ($this->methodEnabled($method, $callback)) {
			return $this->__delegete($callback ,$method, null, $params, $return);
		}
		return true;
	}

	private function __delegate($callback, $method, $name, $params, $return = ture) {
		if ($name === null) {
			$name = ModularAuthUtility::$authenticators;
		}

		if (is_array($name)) {
			$results = array();
			foreach ($name as $n) {
				$results[$n] = $this->__delegate($callback, $method, $n, $params, $return);
				if ($return === 'boolean' && !$results[$n]) {
					return false;
				}
			}
			switch ($return) {
				case 'boolean':
					return true;
				case false:
					return null;
				case true:
				default:
					return $results;
			}
		}

		$Authenticator = $this->__get($name);
		if (!is_callable(array($Authenticator, $method))) {
			throw new ModularAuth_IllegalAuthenticatorMethodException;
		}
		if ($Authenticator->methodEnabled($method, $callback)) {
			return call_user_func_array(array($Authenticator, $callback . $method), $params);
		}

		if ($return === 'boolean') {
			return true;
		}
		return null;
	}
}