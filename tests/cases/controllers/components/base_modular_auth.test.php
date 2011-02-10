<?php

App::import('Lib', 'ModularAuth.ModularAuthTestCase', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));
App::import('Component', 'ModularAuth.MockInterruptModularAuthenticator', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

class BaseModularAuthComponentTestCase extends ModularAuthTestCase {

	public function startTest($method = null) {

		parent::startTest($method);

		$this->_restructComponent();

	}

	protected function _restructComponent($settings = array()) {
		$this->Component = $this->getMock('BaseModularAuthComponent', null, array($this->Collection, $settings));
	}

	public function testTurnCallbackOnOrOff() {

		$this->_restructComponent();

		$this->assertTrue($this->Component->Authenticators->enabled());
		$this->assertTrue($this->Component->disableCallback());
		$this->assertTrue($this->Component->Authenticators->disabled());
		$this->assertTrue($this->Component->enableCallback());
		$this->assertTrue($this->Component->Authenticators->enabled());

	}

	public function testCallParent() {

		$this->assertEqual($this->Component->callParent('password', array('hoge')), Security::hash('hoge', null, true));
		$this->expectException('ModularAuth_IllegalAuthComponentMethodException');
		$this->Component->callParent('undefined_auth_component_method', array('fuga'));

	}

	public function testSetup() {

		$this->_restructComponent();
		$this->assertIsA($this->Component->Controller, 'Controller');
		$this->assertIsA($this->Component->Authenticators, 'ModularAuthenticatorsComponent');
		$this->assertIsA($this->Component->Authenticators->Controller, 'Controller');
		$this->assertIsA($this->Component->Authenticators->Auth, 'AuthComponent');
		$this->assertTrue(ModularAuthUtility::isRegistered('Auth', 'Controller', 'Authenticators'));

		$this->_restructComponent(array('collector' => 'MockModularAuthenticators'));
		$this->assertIsA($this->Component->Authenticators, 'MockModularAuthenticatorsComponent');

		$this->_restructComponent(array('authenticators' => 'MockModularAuthenticator'));
		$this->assertIsA($this->Component->MockModularAuthenticator, 'MockModularAuthenticatorComponent');

		$this->_restructComponent(array('className' => 'ModularAuth'));
		$this->assertIsA($this->Component->Authenticators, 'ModularAuthenticatorsComponent');

	}

	public function testDispatch() {

		$this->_restructComponent(array('collector' => 'MockModularAuthenticators'));
		$this->assertIdentical($this->Component->password('hoge'), Security::hash('hoge', null, true));

		$this->Controller->name = 'tests';
		$this->assertTrue($this->Component->startup($this->Controller));
		$this->assertNull($this->Component->shutdown($this->Controller));

	}

	public function testInterruption() {

		$this->_restructComponent(array('authenticators' => 'ModularAuth.MockInterruptModularAuthenticator'));
		foreach (array_diff(get_class_methods('AuthComponent'), get_class_methods('Object')) as $method) {
			if ($method === 'redirect' || strpos($method, '_') === 0) {
				continue;
			}

			if (in_array($method, array('initialize', 'startup', 'shutdown'))) {
				$args = array($this->Controller);
			} else {
				$args = array('hoge', 'piyo');
			}
			$this->assertEqual(call_user_func_array(array($this->Component, $method), $args), "interrupt $method");
		}

	}
}
