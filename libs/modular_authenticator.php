<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
), false);

abstract class ModularAuthenticator extends ModularAuthBaseObject {
	protected $_callbackMethods = array();

	protected $_overridedResult;
	protected $_resultOverrided = false;
	protected $_interrupted = false;

	public function init($settings = array()) {

		$authMethods = get_class_methods('AuthComponent');
		$objectMethods = get_class_methods('Object');
		foreach (array_diff($authMethods, $objectMethods) as $method) {
			if (strpos($method, '_') !== 0) {
				$this->_callbackMethods[] = strtolower($method);
			}
		}

	}

	public function overrideResult($result) {
		$this->_overridedResult = $result;
		$this->_resultOverrided = true;
	}

	public function interrupt() {
		$this->Auth->Authenticators->interruptResult();
		$this->_interrupted = true;
	}

	protected function _callbackAvailable($method, $callback) {
		if (!in_array(strtolower($method), $this->_callbackMethods) || !in_array(strtolower($callback), array('before', 'after'))) {
			throw new ModularAuth_IllegalAuthenticatorMethodException(strtolower($callback) . Inflector::camelize($method));
		}
		if (!method_exists($this, $callback . $method)) {
			return false;
		}
		return $this->enabled($method, $callback);
	}

	public function dispatchMethod($method, $callback, $params, $return) {
		if (!$this->_callbackAvailable($method, $callback)) {
			return $this->_filterResult($params, $return);
		}

		$result = $this->{$callback . $method}($params);
		return $this->_filterResult($params, $return, $result);
	}

	protected function _filterResult($params, $return, $result = null) {
		if ($this->_interrupted) {
			$this->_interrupted = false;
		} else {
			switch ($return) {
				case 'enchain':
					if ($this->_resultOverrided) {
						$result = array($this->_overridedResult);
						$this->_resultOverrided = false;
					} else {
						$result = $params;
					}
					break;
				case 'boolean':
					if ($return === 'boolean' && $result === null) {
						$result = true;
					}
					break;
			}
		}
		return $result;
	}

	// alias for $this->alias->callParent()
	public function callParent($method) {
		$args = func_get_args();
		/* $method = */ array_shift($args);
		return $this->Auth->callParent($method, $args);
	}

	public function __call($method, $params) {
		if (preg_match('/^parent(.+?)$/i', $method, $matched)) {
			$authMethod = strtolower($matched[1]);
			if (method_exists('AuthComponent', $authMethod)) {
				return $this->Auth->callParent($authMethod, $params);
			}
		}
		throw new ModularAuth_IllegalAuthComponentMethodException($method);
	}
}

