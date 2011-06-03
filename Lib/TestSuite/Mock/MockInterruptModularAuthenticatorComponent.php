<?php

App::uses('ModularAuthenticator', 'ModularAuth.Lib');

class MockInterruptModularAuthenticatorComponent extends ModularAuthenticator {

	 public function beforeinitialize() {
		$this->interrupt();
		return 'interrupt initialize';
	}

	 public function beforestartup() {
		$this->interrupt();
		return 'interrupt startup';
	}

	 public function beforeisAuthorized() {
		$this->interrupt();
		return 'interrupt isAuthorized';
	}

	 public function beforeallow() {
		$this->interrupt();
		return 'interrupt allow';
	}

	 public function beforedeny() {
		$this->interrupt();
		return 'interrupt deny';
	}

	 public function beforemapActions() {
		$this->interrupt();
		return 'interrupt mapActions';
	}

	 public function beforelogin() {
		$this->interrupt();
		return 'interrupt login';
	}

	 public function beforelogout() {
		$this->interrupt();
		return 'interrupt logout';
	}

	 public function beforeuser() {
		$this->interrupt();
		return 'interrupt user';
	}

	 public function beforeredirect() {
		$this->interrupt();
		return 'interrupt redirect';
	}

	 public function beforevalidate() {
		$this->interrupt();
		return 'interrupt validate';
	}

	 public function beforeaction() {
		$this->interrupt();
		return 'interrupt action';
	}

	 public function beforegetModel() {
		$this->interrupt();
		return 'interrupt getModel';
	}

	 public function beforeidentify() {
		$this->interrupt();
		return 'interrupt identify';
	}

	 public function beforehashPasswords() {
		$this->interrupt();
		return 'interrupt hashPasswords';
	}

	 public function beforepassword() {
		$this->interrupt();
		return 'interrupt password';
	}

	 public function beforeshutdown() {
		$this->interrupt();
		return 'interrupt shutdown';
	}

	 public function beforeloggedIn() {
		$this->interrupt();
		return 'interrupt loggedIn';
	}

	 public function beforeconstructAuthenticate() {
		$this->interrupt();
		return 'interrupt constructAuthenticate';
	}

	 public function beforeconstructAuthorize() {
		$this->interrupt();
		return 'interrupt constructAuthorize';
	}

	 public function beforeflash() {
		$this->interrupt();
		return 'interrupt flash';
	}

}
