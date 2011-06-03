<?php

class SecondMockModularAuthenticatorComponent extends ModularAuthenticator {

	public function beforeLogout() {
		return true;
	}

	public function afterLogout() {
		$this->overrideResult('piyo');
	}

}

