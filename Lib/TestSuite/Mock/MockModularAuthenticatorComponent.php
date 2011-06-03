<?php

App::uses('ModularAuthenticator', 'ModularAuth.Lib');

class MockModularAuthenticatorComponent extends ModularAuthenticator {

	public function beforeAllow() {
		return true;
	}

	public function afterAllow() {
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
