<?php

App::import('Component', 'Auth');
App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
), false);

abstract class BaseModularAuthComponent extends AuthComponent {

	public $Authenticators;
	public $Controller;

	public $collector = 'ModularAuth.ModularAuthenticators';
	public $authenticators = array();

	public function __construct($collection, $settings = array()) {

		$settings = $this->_setup($collection->getController(), $settings);
		parent::__construct($collection, $settings);

	}

	protected function _setup($Controller, $settings) {

		ModularAuthUtility::registerObject(compact('Controller') + array('Auth' => $this));

		if (isset($settings['collector'])) {
			$this->collector = $settings['collector'];
			unset($settings['collector']);
		}
		$Authenticators = ModularAuthUtility::loadLibrary('Component', $this->collector, array_diff_key($settings, array('className' => true)));
		ModularAuthUtility::registerObject(compact('Authenticators'));

		if (isset($settings['authenticators'])) {
			$this->authenticators = Set::merge($this->authenticators, $settings['authenticators']);
			unset($settings['authenticators']);
		}

		if (!empty($this->authenticators)) {
			$Authenticators->append($this->authenticators);
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

	public function initialize($Controller) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	public function startup($Controller) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
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
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	public function loggedIn($logged = null) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	public function beforeRender($Controller) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

	public function beforeRedirect($Controller) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);
	}

}