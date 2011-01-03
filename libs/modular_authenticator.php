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
	protected $_interrupted = false;

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
		if (!is_callable(array($this, $callback . $method))) {
			return false;
		}
		return $this->enabled($method, $callback);
	}

	public function dispatchMethod($method, $callback, $params, $return) {
		if (!$this->_callbackAvailable($method, $callback)) {
			return $this->_filterResult($params, $return);
		}

		$result = $this->{$callback . $method}($params)
		return $this->_filterResult($params, $return, $result);
	}

	protected function _filterResult($params, $return, $result = null) {
		if ($this->_interrupted) {
			$this->_interrupted = false;
		} else {
			switch ($return) {
				case 'enchain':
					if ($this->_resultOverrided) {
						$result = $this->_overridedResult;
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
		return call_user_func_array(array($this->Auth, 'callParent'), $args);
	}
}

