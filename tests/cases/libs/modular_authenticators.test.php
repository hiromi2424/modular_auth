<?php

App::import('Lib', 'ModularAuth.ModularAuthenticators');

class ModularAuthenticatorsTestCase extends CakeTestCase {

	function startTest() {
		$this->Authenticators = new ModularAuthenticators;
	}

	public function endTest() {
		ModularAuthUtility::flushObjects();
	}

	public function testMagick() {
		
	}
}