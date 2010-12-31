<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthenticator',
), false);

class MockModularAuthenticatorComponent extends ModularAuthenticator {
	function beforeValidate() {
		return true;
	}

	function afterValidate() {
		$this->overrideResult('hoge');
	}

	function beforePassword() {
		return false;
	}

	function afterPassword() {
		
	}

	function beforeShutdown() {
	}
}

class FirstMockModularAuthenticatorComponent extends ModularAuthenticator {
	function beforeLogout() {
		return false;
	}
	function afterLogout() {
		$this->overrideResult('hoge');
	}
}

class SecondMockModularAuthenticatorComponent extends ModularAuthenticator {
	function beforeLogout() {
		return true;
	}

	function afterLogout() {
		$this->overrideResult('piyo');
	}
}