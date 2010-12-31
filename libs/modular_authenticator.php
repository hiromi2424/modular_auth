<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
), false);

abstract class ModularAuthenticator extends ModularAuthBaseObject {
	protected $_callbackMethods = array(
		'initialize',
		'startup',
		'isauthorized',
		'allow',
		'deny',
		'mapactions',
		'login',
		'logout',
		'user',
		'redirect',
		'validate',
		'action',
		'getmodel',
		'identify',
		'hashpasswords',
		'password',
		'shutdown',
	);

	protected $_overridedResult;
	protected $_resultOverrided = false;

	public function overrideResult($result) {
		$this->_overridedResult = $result;
		$this->_resultOverrided = true;
	}

	protected function _callbackAvailable($method, $callback) {
		if (!in_array(strtolower($method), $this->_callbackMethods) || !in_array(strtolower($callback), array('before', 'after'))) {
			throw new ModularAuth_IllegalAuthenticatorMethodException(strtolower($callback) . Inflector::camelize($method));
		}
		if (!is_callable(array($this, $callback . $method))) {
			return false;
		}
		return $this->enabled($method, $callback);
	}

	public function dispatchMethod($method, $callback, $params, $return) {
		if (!$this->_callbackAvailable($method, $callback)) {
			if ($return === 'enchain') {
				return $params;
			}
			if ($return === 'boolean') {
				return true;
			}
			return null;
		}

		$result = $this->{$callback . $method}($params);

		if ($return === 'enchain') {
			if ($this->_resultOverrided) {
				$result = $this->_overridedResult;
				$this->_resultOverrided = false;
			} else {
				$result = $params;
			}
		} elseif ($return === 'boolean' && $result === null) {
			$result = true;
		}
		return $result;
	}
}

