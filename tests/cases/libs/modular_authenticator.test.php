<?php

App::import('Lib', 'ModularAuth.ModularAuthTestCase', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

class ModularAuthenticatorTestCase extends ModularAuthTestCase {

	public function startTest() {
		ModularAuthUtility::registerObject('Controller', $this->Controller);
		ModularAuthUtility::registerObject('Auth', $this->Auth);
		$this->Authenticator = ModularAuthUtility::loadAuthenticator('MockModularAuthenticator');
	}

	function testInit() {
		$this->Authenticator = new MockModularAuthenticatorComponent;
		$this->Authenticator->init();
		$expectedMethods = array(
			'initialize', 'startup', 'isAuthorized', 'allow', 'deny',
			'mapActions', 'login', 'logout', 'user', 'redirect',
			'validate', 'action', 'getModel', 'identify', 'hashPasswords', 
			'password', 'shutdown',
		);
		foreach ($expectedMethods as $method) {
			$this->assertTrue($this->Authenticator->methodEnabled($method), $method);
		}
	}

	public function testDispatchMethod() {
		// ModularAuthenticator::dispatchMethod($method, $callback, $params, $return)
		$this->assertTrue($this->Authenticator->dispatchMethod('validate', 'before', array(), false));
		$this->assertFalse($this->Authenticator->dispatchMethod('password', 'before', array(), false));
		$this->assertNull($this->Authenticator->dispatchMethod('shutdown', 'before', array(), false));

		$this->assertTrue($this->Authenticator->dispatchMethod('validate', 'before', array(), 'boolean'));
		$this->assertFalse($this->Authenticator->dispatchMethod('password', 'before', array(), 'boolean'));
		$this->assertTrue($this->Authenticator->dispatchMethod('shutdown', 'before', array(), 'boolean'));

		$this->assertIdentical($this->Authenticator->dispatchMethod('validate', 'after', array('result'), 'enchain'), array('hoge'));
		$this->assertIdentical($this->Authenticator->dispatchMethod('password', 'after', array('result'), 'enchain'), array('result'));

		$this->assertIdentical($this->Authenticator->dispatchMethod('action', 'after', array('result'), false), null);
		$this->assertIdentical($this->Authenticator->dispatchMethod('action', 'after', array('result'), 'boolean'), true);
		$this->assertIdentical($this->Authenticator->dispatchMethod('action', 'after', array('result'), 'enchain'), array('result'));


		try {
			$this->Authenticator->dispatchMethod('illegalMethod', 'before', array('result'), 'enchain');
			$this->fail('Illegal Method Exception was not thrown when illegal method was given : %s');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_IllegalAuthenticatorMethodException');
		}

		try {
			$this->Authenticator->dispatchMethod('action', 'illegalCallback', array('result'), 'enchain');
			$this->fail('Illegal Method Exception was not thrown when illegal callback was given : %s');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_IllegalAuthenticatorMethodException');
		}
	}

	public function testCallParent() {
		$this->Auth->setReturnValue('callParent', false, array('login'));
		$this->Auth->setReturnValue('callParent', true, array('login', array('success')));
		$this->assertFalse($this->Authenticator->callParent('login'));
		$this->assertTrue($this->Authenticator->callParent('login', 'success'));
	}

	public function testMagickCallParent() {
		$this->Auth->setReturnValue('callParent', false, array('login'));
		$this->Auth->setReturnValue('callParent', true, array('login', array('success')));
		$this->assertFalse($this->Authenticator->parentLogin());
		$this->assertTrue($this->Authenticator->parentLogin('success'));

		$this->expectException('ModularAuth_IllegalAuthComponentMethodException');
		$this->Authenticator->parentUndefinedMethod();
	}

	function testInterrupt() {
		$this->Auth->Authenticators = ModularAuthUtility::loadLibrary('Lib', 'ModularAuthenticators');
		$this->assertIdentical($this->Authenticator->dispatchMethod('login', 'before', array(), 'boolean'), 'interrupted');
	}
}