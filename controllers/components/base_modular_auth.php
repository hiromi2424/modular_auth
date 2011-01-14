<?php

App::import('Component', 'Auth');
App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
), false);

abstract class BaseModularAuthComponent extends AuthComponent {

	public $Authenticators;
	public $Controller;

	public $collector = 'ModularAuth.ModularAuthenticators';

	protected function _setup($Controller, $settings) {
		ModularAuthUtility::registerObject(compact('Controller') + array('Auth' => $this));

		if (isset($settings['collector'])) {
			$this->collector = $settings['collector'];
			unset($settings['collector']);
		}
		$Authenticators = ModularAuthUtility::loadLibrary('Lib', $this->collector);
		ModularAuthUtility::registerObject(compact('Authenticators'));
		$Authenticators->configure($settings);

		if (isset($settings['authenticators'])) {
			$Authenticators->append($settings['authenticators']);
			unset($settings['authenticators']);
		}
		ModularAuthUtility::bindObject($this, 'Controller', 'Authenticators');
		return $settings;
	}

	public function callParent($method, $params = array()) {
		if (!method_exists('AuthComponent', $method)) {
			throw new ModularAuth_IllegalAuthComponentMethodException($method);
		}
		return call_user_func_array(array('parent', $method), $params);
	}

	public function disableCallBack($callback = true) {
		return $this->Authenticators->disable($callback);
	}

	public function enableCallback($callback = true) {
		return $this->Authenticators->enable($callback);
	}

	protected function _dispatch($method, $params, $beforeReturn = 'boolean', $afterRetrun = 'enchain') {
		$result = $this->Authenticators->triggerCallback('before', $method, $params, $beforeReturn);
		if (!$this->Authenticators->interrupted) {
			$result = $this->callParent($method, $params);
		}
		$result = $this->Authenticators->triggerCallback('after', $method, array($result), $afterRetrun);
		return $result;
	}

	public function initialize($Controller, $settings = array()) {
		$params = array($Controller, $settings);
		$settings = $this->dispatchMethod('_setup', $params);

		$result = $this->Authenticators->triggerCallback('before', 'initialize', $params);
		if (!$this->Authenticators->interrupted) {
			// workaround warning error with 'expected argument 1 as a reference'
			$result = parent::initialize($Controller, $settings);
		}
		$result = $this->Authenticators->triggerCallback('after', 'initialize', array($result), 'enchain');
		return $result;
	}

	public function startup($Controller) {
		$result = $this->Authenticators->triggerCallback('before', 'startup', array($Controller));
		if (!$this->Authenticators->interrupted) {
			// workaround warning error with 'expected argument 1 as a reference'
			$result = parent::startup($Controller);
		}
		$result = $this->Authenticators->triggerCallback('after', 'startup', array($result), 'enchain');
		return $result;
	}

	 public function isAuthorized($type = null, $object = null, $user = null) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function allow() {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function deny() {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function mapActions($map = array()) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function login($data = null) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function logout() {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function user($key = null) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function redirect($url = null) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function validate($object, $user = null, $action = null) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function action($action = ':plugin/:controller/:action') {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function getModel($name = null) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function identify($user = null, $conditions = null) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function hashPasswords($data) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function password($password) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	 public function shutdown($Controller) {
		$result = $this->Authenticators->triggerCallback('before', 'shutdown', array($Controller));
		if (!$this->Authenticators->interrupted) {
			// workaround warning error with 'expected argument 1 as a reference'
			$result = parent::shutdown($Controller);
		}
		$result = $this->Authenticators->triggerCallback('after', 'shutdown', array($result), 'enchain');
		return $result;
	}

}