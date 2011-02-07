<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
	'ModularAuth.ModularAuthenticator',
), false);

class ModularAuthenticatorsComponent extends ModularAuthBaseObject implements ArrayAccess {

	public $interrupted = false;

	protected $_results;
	protected $_name;
	protected $_return;
	protected $_params;


	public function interruptResult($interrupt = true) {
		$this->interrupted = !!$interrupt;
	}

	public function triggerCallback($callback, $method, $params, $return = 'boolean') {

		$this->interrupted = false;
		$this->_params = $params;
		$this->_return = $return;
		$this->_results = array();

		if (!empty(ModularAuthUtility::$authenticators) && $this->methodEnabled($method, $callback)) {
			foreach (ModularAuthUtility::$authenticators as $this->_name) {

				$result = $this->__get($this->_name)->dispatchMethod($method, $callback, $this->_params, $return);
				if (!$this->_filterResult($result)) {
					return $result;
				}

			}
		}
		return $this->_filterResults();

	}

	protected function _filterResult($result) {

		if ($this->interrupted) {
			return false;
		}
		switch ($this->_return) {

			case false:
				break;

			case 'boolean':
				if (!$result) {
					return false;
				}
				break;

			case 'enchain':
				$this->_params = $result;
				break;

			case 'array':
				$this->_results[$this->_name] = $result;
				break;

			default:
				throw new ModularAuth_IllegalArgumentException("\$return = $this->_return");

		}

		return true;

	}

	protected function _filterResults() {

		switch ($this->_return) {
			case false:
				return null;
			case 'boolean':
				return empty($this->_results);
			case 'enchain':
				return current($this->_params);
			case 'array':
				return $this->_results;
			default:
				throw new ModularAuth_IllegalArgumentException("\$return = $this->_return");
		}

	}

	public function get($name) {

		return $this->__get($name);

	}

	public function append($name, $settings = array()) {

		if (is_array($name)) {

			foreach (Set::normalize($name) as $n => $settings) {
				$this->__set($n, $settings);
			}

			return true;

		}
		return $this->__set($name, $settings);

	}

	public function exists($name) {
		return $this->__isset($name);
	}

	public function drop($name) {
		return $this->__unset($name);
	}

	public function offsetGet($offset) {
		return $this->__get($offset);
	}

	public function offsetSet($offset, $value) {
		return $this->__set($offset, $value);
	}

	public function offsetExists($offset) {
		return $this->__isset($offset);
	}

	public function offsetUnset($offset) {
		return $this->__unset($offset);
	}

	public function __get($name) {
		return ModularAuthUtility::getObject($name);
	}

	public function __set($name, $value) {

		try {

			if (empty($name)) {
				throw new ModularAuth_IllegalAuthenticatorNameException(var_export($name, true));
			} elseif (is_object($value)) {

				if (!($value instanceof ModularAuthenticator)) {
					throw new ModularAuth_IllegalAuthenticatorObjectException(get_class($value));
				}

				ModularAuthUtility::registerObject($name, $value);
				return $this->__get($name);

			} elseif (!$this->__isset($name)) {
				$Authenticator = ModularAuthUtility::loadAuthenticator($name, $value);
			} else {
				$Authenticator = $this->__get($name);
				$Authenticator->configure($value);
			}

			return $Authenticator;

		} catch (ModularAuth_ObjectNotFoundException $e) {
			throw new ModularAuth_AuthenticatorNotFoundException($e->getMessage());
		}

	}

	public function __isset($name) {
		return ModularAuthUtility::isRegistered($name);
	}

	public function __unset($name) {
		return ModularAuthUtility::deleteObject($name);
	}

}