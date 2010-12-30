<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
), false);

abstract class ModularAuthenticator {
	public $Controller;
	public $Auth;

	protected $_disabled;
	protected $_disabledMethods = array();

	public function init() {
	}

	public function configure($settings = array()) {
		if ($settings !== array() && !is_array($settings)) {
			$settings = array();
		}

		$this->_set($settings);
	}

	public function disable($method = null, $callback = true) {
		
	}

	public function enable($method = null, $callback = true) {
		
	}

	public function enabled($method = null, $callback = true) {
		
	}

	public function disabled($method = null, $callback = true) {
		
	}

	public function call__($method, $params) {
		throw new ModularAuth_IllegalAuthenticatorMethodException;
	}
}

