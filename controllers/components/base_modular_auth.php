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

	protected function _setup(Controller $Controller, $settings) {
		ModularAuthUtility::regsiterObject(compact('Controller') + array('Auth' => $this));

		if (isset($settings['collector'])) {
			$this->collector = $settings['collector'];
			unset($settings['collector']);
		}
		$Authenticators = ModularAuthUtility::loadLibrary('Lib', $this->collector);
		ModularAuthUtility::regsiterObject(compact('Authenticators'));
		$Authenticators->configure($settings);

		if (isset($settings['authenticators'])) {
			$Authenticators->reset($setting['authenticators']);
			unset($settings['authenticators']);
		}
		ModularAuthUtility::bindObject($this, 'Controller', 'Authenticators');
		return $settings;
	}

	public function disableCallBack($callback = true) {
		return $this->Authenticators->disable($callback);
	}

	public function enableCallback($callback = true) {
		return $this->Authenticators->enable($callback);
	}

	public function initialize(Controller $Controller, $settings = array()) {
		$settings = $this->_setup($Controller, $settings);

		$result = parent::initialize($Controller, $settings);
	}

	public function startup(Controller $Controller) {

		$result = parent::startup($Controller, $settings);

	}
}