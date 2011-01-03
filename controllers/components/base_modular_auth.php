<?php

App::import('Component', 'Auth');
App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
	'ModularAuth.ModularAuthenticators',
), false);

abstract class BaseModularAuthComponent extends AuthComponent {
	public $components = array(
		'Session',
		'Hack.Alias',
	);

	public $Authenticators;
	public $Controller;

	public $collector = 'ModularAuth.ModularAuthenticators';

	protected function _setup($Controller, $settings) {
		ModularAuthUtility::regsiterObject(compact('Controller') + array('Auth' => $this));

		if (isset($settings['collector'])) {
			$this->collector = $settings['collector'];
			unset($settings['collector']);
		}
		$Authenticators = ModularAuthUtility::loadLibrary('Lib', $this->collector);
		ModularAuthUtility::regsiterObject(compact('Authenticators'));
		$Authenticators->configure($settings);

		if (isset($settings['authenticators'])) {
			$Authenticators->append($setting['authenticators']);
			unset($settings['authenticators']);
		}
		ModularAuthUtility::bindObject($this, 'Controller', 'Authenticators');
		return $settings;
	}

	public function callParent($method) {
		$args = func_get_args();
		/* $method = */ array_shift($args);

		return call_user_func_array(array('parent', $method), $args);
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
			array_unshift($params, $method);
			$result = $this->dispatchMethod('callParent', $params);
		}
		$result = $this->Authenticators->triggerCallback('after', $method, array($result), $afterRetrun);
		return $result;
	}

	public function initialize($Controller, $settings = array()) {
		$args = func_get_args();
		$settings = $this->dispatchMethod('_setup', $args);

		return $this->_dispatch(__FUNCTION__, $args);
	}

	public function startup($Controller) {
		$args = func_get_args();
		return $this->_dispatch(__FUNCTION__, $args);

	}
}