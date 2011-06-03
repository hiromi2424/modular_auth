<?php

App::uses('ModularAuthTestCase', 'ModularAuth.TestSuite');

class ModularAuthenticatorTest extends ModularAuthTestCase {

	public function startTest($method) {

		parent::startTest($method);

		ModularAuthUtility::registerObject('Controller', $this->Controller);
		ModularAuthUtility::registerObject('Auth', $this->Auth);
		$this->Authenticator = ModularAuthUtility::loadAuthenticator('MockModularAuthenticator');

	}

	public function testInit() {

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
		$this->assertTrue($this->Authenticator->dispatchMethod('allow', 'before', array(), false));
		$this->assertFalse($this->Authenticator->dispatchMethod('password', 'before', array(), false));
		$this->assertNull($this->Authenticator->dispatchMethod('shutdown', 'before', array(), false));

		$this->assertTrue($this->Authenticator->dispatchMethod('allow', 'before', array(), 'boolean'));
		$this->assertFalse($this->Authenticator->dispatchMethod('password', 'before', array(), 'boolean'));
		$this->assertTrue($this->Authenticator->dispatchMethod('shutdown', 'before', array(), 'boolean'));

		$this->assertIdentical($this->Authenticator->dispatchMethod('allow', 'after', array('result'), 'enchain'), array('hoge'));
		$this->assertIdentical($this->Authenticator->dispatchMethod('password', 'after', array('result'), 'enchain'), array('result'));

		$this->assertIdentical($this->Authenticator->dispatchMethod('deny', 'after', array('result'), false), null);
		$this->assertIdentical($this->Authenticator->dispatchMethod('deny', 'after', array('result'), 'boolean'), true);
		$this->assertIdentical($this->Authenticator->dispatchMethod('deny', 'after', array('result'), 'enchain'), array('result'));


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

		$this->Auth->expects($this->any())->method('callParent')->will($this->onConsecutiveCalls(false, true));

		$this->assertFalse($this->Authenticator->callParent('login'));
		$this->assertTrue($this->Authenticator->callParent('logout', 'success'));

	}

	public function testMagickCallParent() {

		$this->Auth->expects($this->any())->method('callParent')->will($this->onConsecutiveCalls(false, true));

		$this->assertFalse($this->Authenticator->parentLogin());
		$this->assertTrue($this->Authenticator->parentLogin('success'));

		$this->expectException('ModularAuth_IllegalAuthComponentMethodException');
		$this->Authenticator->parentUndefinedMethod();

	}

	public function testInterrupt() {

		$this->Auth->expects($this->never())->method('callParent');
		$this->Auth->Authenticators = ModularAuthUtility::loadLibrary('Component', 'ModularAuthenticators');
		$this->assertIdentical($this->Authenticator->dispatchMethod('login', 'before', array(), 'boolean'), 'interrupted');

	}

	public function testArguments() {

		$this->Auth->Authenticators = ModularAuthUtility::loadLibrary('Component', 'ModularAuthenticators');
		$this->assertIdentical($this->Authenticator->dispatchMethod('initialize', 'before', array(1, 2), 'boolean'), array(1, 2));

	}

}