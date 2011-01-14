<?php

App::import('Lib', 'ModularAuth.ModularAuthTestCase', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));
App::import('Component', 'ModularAuth.MockInterruptModularAuthenticator', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

class BaseModularAuthComponentTestCase extends ModularAuthTestCase {
	function startTest($method = null) {
		$this->Component = new MockBaseModularAuthComponent;
		parent::startTest($method);
	}

	function testTurnCallbackOnOrOff() {
		$this->Component->initialize($this->Controller);
		$this->assertTrue($this->Component->Authenticators->enabled());
		$this->assertTrue($this->Component->disableCallback());
		$this->assertTrue($this->Component->Authenticators->disabled());
		$this->assertTrue($this->Component->enableCallback());
		$this->assertTrue($this->Component->Authenticators->enabled());
	}

	function testCallParent() {
		$this->assertEqual($this->Component->callParent('password', array('hoge')), Security::hash('hoge', null, true));
		$this->expectException('ModularAuth_IllegalAuthComponentMethodException');
		$this->Component->callParent('undefined_auth_component_method', array('fuga'));
	}

	function testSetup() {
		$this->Component->initialize($this->Controller);
		$this->assertIsA($this->Component->Controller, 'Controller');
		$this->assertIsA($this->Component->Authenticators, 'ModularAuthenticators');
		$this->assertIsA($this->Component->Authenticators->Controller, 'Controller');
		$this->assertIsA($this->Component->Authenticators->Auth, 'AuthComponent');
		$this->assertTrue(ModularAuthUtility::isRegistered('Auth', 'Controller', 'Authenticators'));

		$this->startTest();
		$this->Component->initialize($this->Controller, array('collector' => 'MockModularAuthenticators'));
		$this->assertIsA($this->Component->Authenticators, 'MockModularAuthenticators');

		$this->startTest();
		$this->Component->initialize($this->Controller, array('authenticators' => 'MockModularAuthenticator'));
		$this->assertIsA($this->Component->MockModularAuthenticator, 'MockModularAuthenticatorComponent');
	}

	function testDispatch() {
		$this->Component->initialize($this->Controller, array('collector' => 'MockModularAuthenticators'));
		$this->assertIdentical($this->Component->password('hoge'), Security::hash('hoge', null, true));

		$this->Controller->name = 'tests';
		$this->assertTrue($this->Component->startup($this->Controller));
		$this->assertNull($this->Component->shutdown($this->Controller));
	}

	function testInterruption() {
		$this->Component->initialize($this->Controller, array('authenticators' => 'ModularAuth.MockInterruptModularAuthenticator'));
		foreach (array_diff(get_class_methods('AuthComponent'), get_class_methods('Object')) as $method) {
			if ($method === 'initialize' || strpos($method, '_') === 0) {
				continue;
			}

			if (in_array($method, array('startup', 'shutdown'))) {
				$args = array($this->Controller);
			} else {
				$args = array('hoge', 'piyo');
			}
			$this->assertEqual(call_user_func_array(array($this->Component, $method), $args), "interrupt $method");
		}
	}
}
