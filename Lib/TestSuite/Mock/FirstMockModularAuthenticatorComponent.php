<?php

class FirstMockModularAuthenticatorComponent extends ModularAuthenticator {

	public function beforeLogout() {
		return false;
	}
	public function afterLogout() {
		$this->overrideResult('hoge');
	}
}

