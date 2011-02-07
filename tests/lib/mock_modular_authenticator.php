<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthenticator',
), false);

class MockModularAuthenticatorComponent extends ModularAuthenticator {

	public function beforeValidate() {
		return true;
	}

	public function afterValidate() {
		$this->overrideResult('hoge');
	}

	public function beforePassword() {
		return false;
	}

	public function afterPassword() {
		
	}

	public function beforeShutdown() {
	}

	public function beforeLogin() {

		$this->interrupt();
		return 'interrupted';

	}

	public function beforeInitialize() {

		$args = func_get_args();
		return $args;

	}

}

class FirstMockModularAuthenticatorComponent extends ModularAuthenticator {

	public function beforeLogout() {
		return false;
	}
	public function afterLogout() {
		$this->overrideResult('hoge');
	}

}

class SecondMockModularAuthenticatorComponent extends ModularAuthenticator {

	public function beforeLogout() {
		return true;
	}

	public function afterLogout() {
		$this->overrideResult('piyo');
	}

}

class MockModularAuthenticatorsComponent extends ModularAuthenticatorsComponent {
	
}