<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthUtility',
), false);

abstract class ModularAuthBaseObject extends Object {
	public $Controller;
	public $Auth;

	protected $_disabled = false;
	protected $_disabledMethods = array();

	public function init() {
		
	}

	public function configure($settings) {
		extract($settings);
		if (isset($disable)) {
			$this->disable($disable);
		}
		if (isset($disableMethods)) {
			foreach (Set::normalize($disableMethods) as $method => $callback) {
				$this->disableMethod($method, $callback);
			}
		}

		if (isset($enable)) {
			$this->enable($enable);
		}
		if (isset($enableMethods)) {
			foreach (Set::normalize($enableMethods) as $method => $callback) {
				$this->enableMethod($method, $callback);
			}
		}
	}

	public function enable() {
		return $this->_callStateMethod('enableState', func_get_args());
	}

	public function disable() {
		return $this->_callStateMethod('disableState', func_get_args());
	}

	public function enabled() {
		return $this->_callStateMethod('enabled', func_get_args());
	}

	public function disabled() {
		return $this->_callStateMethod('disabled', func_get_args());
	}

	public function enableMethod($method, $callback = true) {
		return $this->enable($method, $callback);
	}

	public function disableMethod($method, $callback = true) {
		return $this->disable($method, $callback);
	}

	public function methodEnabled($method, $callback = true) {
		return $this->enabled($callback) && $this->enabled($method, $callback);
	}

	public function methodDisabled($method, $callback = true) {
		return $this->disabled($callback) || $this->disabled($method, $callback);
	}

	protected function _callStateMethod($call, $args) {
		$callback = true;
		$method = null;
		switch (count($args)) {
			case 0:
				break;
			case 1:
				$callback = $args[0];
				break;
			case 2:
				$method = $args[0];
				$callback = $args[1];
				break;
			default:
				throw new ModularAuth_IllegalArgumentsException;
		}

		if ($method === 'all') {
			$method = null;
		}

		if ($callback === null) {
			$callback = true;
		}

		if (!isset($method)) {
			return ModularAuthUtility::$call($this->_disabled, $callback);
		} else {
			if (!isset($this->_disabledMethods[$method])) {
				$this->_disabledMethods[$method] = false;
			}
			return ModularAuthUtility::$call($this->_disabledMethods[$method], $callback);
		}
	}
}