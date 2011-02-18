<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthException',
	'ModularAuth.ModularAuthUtility',
	'ModularAuth.ModularAuthBaseObject',
	'ModularAuth.ModularAuthenticator',
), false);

App::import('Cotroller', 'Controller', false);
App::import('Component', 'Auth', false);
App::import('Component', 'ModularAuth.ModularAuth', false);
App::import('Component', 'ModularAuth.ModularAuthenticators', false);

App::import('Component', 'ModularAuth.MockModularAuthenticator', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

abstract class ModularAuthTestCase extends CakeTestCase {

	public function startTest($method) {

		$this->Controller = $this->getMock('Controller');
		$this->Controller->constructClasses();

		$this->Controller->request = new CakeRequest('/');
		$this->Controller->request->addParams(array(
			'controller' => 'Test',
			'action' => 'hoge',
		));

		$this->Collection = $this->Controller->Components;
		$this->Collection->init($this->Controller);

		$this->Auth = $this->getMock('ModularAuthComponent', array(), array($this->Collection, array()));
		parent::startTest($method);

	}

	public function endTest($method) {

		ModularAuthUtility::flushObjects();
		parent::endTest($method);

	}

}