<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthBaseObject',
	'ModularAuth.ModularAuthBaseObject',
	'ModularAuth.ModularAuthBaseObject',
	'ModularAuth.ModularAuthenticators',
	'ModularAuth.ModularAuthenticator',
), false);
App::import('Component', 'ModularAuth.MockModularAuthenticator', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

App::import('Cotroller', 'Controller', false);
App::import('Component', 'Auth', false);
App::import('Component', 'ModularAuth.ModularAuth', false);

Mock::generate('Controller');
Mock::generate('ModularAuthComponent');
Mock::generatePartial('ModularAuthBaseObject', 'MockModularAuthBaseObject', array('log'));

class ModularAuthTestCase extends CakeTestCase {

	public function startCase() {
		$this->Auth = new MockModularAuthComponent;
		$this->Controller = new MockController;
	}

	public function endTest() {
		ModularAuthUtility::flushObjects();
	}
}