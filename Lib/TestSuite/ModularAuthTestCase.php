<?php

App::uses('ModularAuthException', 'ModularAuth.Lib');
App::uses('ModularAuthUtility', 'ModularAuth.Lib');
App::uses('ModularAuthBaseObject', 'ModularAuth.Lib');
App::uses('ModularAuthenticator', 'ModularAuth.Lib');

App::uses('Controller', 'Controller');
App::uses('AuthComponent', 'Controller/Component');
App::uses('BaseModularAuthComponent', 'ModularAuth.Controller/Component');
App::uses('ModularAuthComponent', 'ModularAuth.Controller/Component');
App::uses('ModularAuthenticatorsComponent', 'ModularAuth.Controller/Component');

App::uses('MockModularAuthenticatorComponent', 'ModularAuth.TestSuite/Mock');
App::uses('MockModularAuthenticatorsComponent', 'ModularAuth.TestSuite/Mock');
App::uses('MockInterruptModularAuthenticatorComponent', 'ModularAuth.TestSuite/Mock');
App::uses('FirstMockModularAuthenticatorComponent', 'ModularAuth.TestSuite/Mock');
App::uses('SecondMockModularAuthenticatorComponent', 'ModularAuth.TestSuite/Mock');

abstract class ModularAuthTestCase extends CakeTestCase {

	public function startTest($method) {
		parent::startTest($method);

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

	}

	public function endTest($method) {

		ModularAuthUtility::flushObjects();
		parent::endTest($method);

	}

}