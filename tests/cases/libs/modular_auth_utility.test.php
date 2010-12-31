<?php

App::import('Lib', 'ModularAuth.ModularAuthTestCase', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

class ModularAuthUtilityTest extends ModularAuthTestCase {

	public function testLoadLibrary() {

		$this->assertIsA(ModularAuthUtility::loadLibrary('Component', 'Cookie'), 'CookieComponent');

		try {
			ModularAuthUtility::loadLibrary('Lib', 'ModularAuth.ModularAuthenticators');
			$this->fail('Unexpected objects Controller, Auth was found %s');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_UnregisteredObjectException');
		}

		ModularAuthUtility::registerObject('Auth', $this->Auth);
		ModularAuthUtility::registerObject('Controller', $this->Controller);
		$Authenticators = ModularAuthUtility::loadLibrary('Lib', 'ModularAuth.ModularAuthenticators');
		$this->assertIsA($Authenticators, 'ModularAuthenticators');
		$this->assertIsA($Authenticators->Auth, 'AuthComponent');
		$this->assertIsA($Authenticators->Controller, 'Controller');

		$this->expectException('ModularAuth_ObjectNotFoundException');
		ModularAuthUtility::loadLibrary('Core', 'WrongFile');
	}

	public function testLoadAuthenticator() {
		ModularAuthUtility::registerObject('Auth', $this->Auth);
		ModularAuthUtility::registerObject('Controller', $this->Controller);
		$Authenticator = ModularAuthUtility::loadAuthenticator('MockModularAuthenticator');
		$this->assertIsA($Authenticator, 'MockModularAuthenticatorComponent');
		$this->assertIsA($Authenticator->Auth, 'AuthComponent');
		$this->assertIsA($Authenticator->Controller, 'Controller');
		$this->assertIsA($Authenticator->Auth->MockModularAuthenticator, 'ModularAuthenticator');
		$this->assertTrue(ModularAuthUtility::isRegistered('MockModularAuthenticator'));

		$this->assertEqual(ModularAuthUtility::$authenticators, array('MockModularAuthenticator'));
		ModularAuthUtility::deleteObject('MockModularAuthenticator');
		$this->assertEqual(ModularAuthUtility::$authenticators, array());
		$this->assertFalse(isset($Authenticator->Auth->MockModularAuthenticator));
	}

	public function testNormalizeMethods() {
		$this->assertEqual(ModularAuthUtility::normalize('hoge.piyo_fuga'), 'Hoge_PiyoFuga');
		$this->assertEqual(ModularAuthUtility::normalize('Hoge.PiyoFuga'), 'Hoge_PiyoFuga');
		$this->assertEqual(ModularAuthUtility::normalize('Hoge_PiyoFuga'), 'Hoge_PiyoFuga');
		$this->assertEqual(ModularAuthUtility::denormalize('hoge.piyo_fuga'), 'Hoge.PiyoFuga');
		$this->assertEqual(ModularAuthUtility::denormalize('Hoge.PiyoFuga'), 'Hoge.PiyoFuga');
		$this->assertEqual(ModularAuthUtility::denormalize('Hoge_PiyoFuga'), 'Hoge.PiyoFuga');

		$this->assertEqual(ModularAuthUtility::normalize('hoge_moge.piyo_fuga'), 'HogeMoge_PiyoFuga');
		$this->assertEqual(ModularAuthUtility::denormalize('hoge_moge.piyo_fuga'), 'HogeMoge.PiyoFuga');
	}

	public function testChangeState() {
		$property = false;
		$this->assertTrue(ModularAuthUtility::disableState($property, true));
		$this->assertIdentical($property, true);

		$property = false;
		$this->assertTrue(ModularAuthUtility::disableState($property, 'before'));
		$this->assertIdentical($property, 'before');

		$property = false;
		$this->assertTrue(ModularAuthUtility::disableState($property, 'after'));
		$this->assertIdentical($property, 'after');

		$property = false;
		$this->assertFalse(ModularAuthUtility::enableState($property, true));
		$this->assertIdentical($property, false);

		$property = false;
		$this->assertFalse(ModularAuthUtility::enableState($property, 'before'));
		$this->assertIdentical($property, false);

		$property = false;
		$this->assertFalse(ModularAuthUtility::enableState($property, 'after'));
		$this->assertIdentical($property, false);


		$property = 'before';
		$this->assertTrue(ModularAuthUtility::disableState($property, true));
		$this->assertIdentical($property, true);

		$property = 'before';
		$this->assertFalse(ModularAuthUtility::disableState($property, 'before'));
		$this->assertIdentical($property, 'before');

		$property = 'before';
		$this->assertTrue(ModularAuthUtility::disableState($property, 'after'));
		$this->assertIdentical($property, true);

		$property = 'before';
		$this->assertTrue(ModularAuthUtility::enableState($property, true));
		$this->assertIdentical($property, false);

		$property = 'before';
		$this->assertTrue(ModularAuthUtility::enableState($property, 'before'));
		$this->assertIdentical($property, false);

		$property = 'before';
		$this->assertFalse(ModularAuthUtility::enableState($property, 'after'));
		$this->assertIdentical($property, 'before');


		$property = 'after';
		$this->assertTrue(ModularAuthUtility::disableState($property, true));
		$this->assertIdentical($property, true);

		$property = 'after';
		$this->assertTrue(ModularAuthUtility::disableState($property, 'before'));
		$this->assertIdentical($property, true);

		$property = 'after';
		$this->assertFalse(ModularAuthUtility::disableState($property, 'after'));
		$this->assertIdentical($property, 'after');

		$property = 'after';
		$this->assertTrue(ModularAuthUtility::enableState($property, true));
		$this->assertIdentical($property, false);

		$property = 'after';
		$this->assertFalse(ModularAuthUtility::enableState($property, 'before'));
		$this->assertIdentical($property, 'after');

		$property = 'after';
		$this->assertTrue(ModularAuthUtility::enableState($property, 'after'));
		$this->assertIdentical($property, false);


		$property = true;
		$this->assertFalse(ModularAuthUtility::disableState($property, true));
		$this->assertIdentical($property, true);

		$property = true;
		$this->assertFalse(ModularAuthUtility::disableState($property, 'before'));
		$this->assertIdentical($property, true);

		$property = true;
		$this->assertFalse(ModularAuthUtility::disableState($property, 'after'));
		$this->assertIdentical($property, true);

		$property = true;
		$this->assertTrue(ModularAuthUtility::enableState($property, true));
		$this->assertIdentical($property, false);

		$property = true;
		$this->assertTrue(ModularAuthUtility::enableState($property, 'before'));
		$this->assertIdentical($property, 'after');

		$property = true;
		$this->assertTrue(ModularAuthUtility::enableState($property, 'after'));
		$this->assertIdentical($property, 'before');
	}

	public function testCompareState() {
		$this->assertIdentical(ModularAuthUtility::disabled(true, true), true);
		$this->assertIdentical(ModularAuthUtility::disabled(true, 'before'), true);
		$this->assertIdentical(ModularAuthUtility::disabled(true, 'after'), true);

		$this->assertIdentical(ModularAuthUtility::disabled('before', true), true);
		$this->assertIdentical(ModularAuthUtility::disabled('before', 'before'), true);
		$this->assertIdentical(ModularAuthUtility::disabled('before', 'after'), false);

		$this->assertIdentical(ModularAuthUtility::disabled('after', true), true);
		$this->assertIdentical(ModularAuthUtility::disabled('after', 'before'), false);
		$this->assertIdentical(ModularAuthUtility::disabled('after', 'after'), true);

		$this->assertIdentical(ModularAuthUtility::disabled(false, true), false);
		$this->assertIdentical(ModularAuthUtility::disabled(false, 'before'), false);
		$this->assertIdentical(ModularAuthUtility::disabled(false, 'after'), false);


		$this->assertIdentical(ModularAuthUtility::enabled(true, true), false);
		$this->assertIdentical(ModularAuthUtility::enabled(true, 'before'), false);
		$this->assertIdentical(ModularAuthUtility::enabled(true, 'after'), false);

		$this->assertIdentical(ModularAuthUtility::enabled('before', true), false);
		$this->assertIdentical(ModularAuthUtility::enabled('before', 'before'), false);
		$this->assertIdentical(ModularAuthUtility::enabled('before', 'after'), true);

		$this->assertIdentical(ModularAuthUtility::enabled('after', true), false);
		$this->assertIdentical(ModularAuthUtility::enabled('after', 'before'), true);
		$this->assertIdentical(ModularAuthUtility::enabled('after', 'after'), false);

		$this->assertIdentical(ModularAuthUtility::enabled(false, true), true);
		$this->assertIdentical(ModularAuthUtility::enabled(false, 'before'), true);
		$this->assertIdentical(ModularAuthUtility::enabled(false, 'after'), true);
	}

	public function testObjectMethods() {
		$destination = new Object;


		$registered = new Object;
		ModularAuthUtility::registerObject('registered', $registered);
		ModularAuthUtility::bindObject($destination, 'registered');
		$this->assertTrue($destination->Registered === $registered);
		$this->assertTrue(ModularAuthUtility::isRegistered($registered));

		$Registered = new Object;
		$Registered->testVar = 1;
		ModularAuthUtility::registerObject(compact('Registered'));
		$this->assertFalse(isset($destination->Registered->testVar));
		ModularAuthUtility::bindObject($destination, 'Registered');
		$this->assertIdentical($destination->Registered->testVar, 1);

		ModularAuthUtility::unbindObject($destination, 'Registered');
		$this->assertFalse(isset($destination->Registered));

		ModularAuthUtility::registerObject('one', new Object);
		ModularAuthUtility::registerObject('two', new Object);
		ModularAuthUtility::registerObject('three', new Object);
		$this->assertTrue(ModularAuthUtility::isRegistered('One', 'Two', 'Three'));
		$this->assertIsA(ModularAuthUtility::getObject('one'), 'Object');


		ModularAuthUtility::bindObject($destination, array('one', array('two')), array(array(array('three'))));
		$this->assertTrue(isset($destination->One));
		$this->assertTrue(isset($destination->Two));
		$this->assertTrue(isset($destination->Three));
		ModularAuthUtility::bindObject('One', 'Two');
		$this->assertTrue(isset($destination->One->Two));
		ModularAuthUtility::unbindObject($destination, 'one', 'two', array(array('three', 'two', 'one')));
		$this->assertFalse(isset($destination->One));
		$this->assertFalse(isset($destination->Two));
		$this->assertFalse(isset($destination->Three));

		$this->assertTrue(ModularAuthUtility::isRegistered('Registered', 'One', 'Two', 'Three'));
		ModularAuthUtility::deleteObject('One');
		$this->assertFalse(ModularAuthUtility::isRegistered('Registered', 'One', 'Two', 'Three'));

		$this->assertTrue(ModularAuthUtility::flushObjects());
		$this->assertFalse(ModularAuthUtility::flushObjects());

		$this->assertFalse(ModularAuthUtility::isRegistered($Registered));
		$this->assertNull(ModularAuthUtility::isRegistered($fp = fopen(__FILE__, 'r')));
		fclose($fp);

		ModularAuthUtility::registerObject('Auth', $this->Auth);
		ModularAuthUtility::registerObject('Controller', $this->Controller);
		ModularAuthUtility::loadAuthenticator('MockModularAuthenticator');
		$this->assertTrue(ModularAuthUtility::flushObjects());

		try {
			ModularAuthUtility::bindObject($destination, 'UnredisteredObject');
			$this->fail('ModularAuthUtility::bindObject(UnredisteredObject) %s');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_UnregisteredObjectException');
		}

		try {
			ModularAuthUtility::unbindObject($destination, 'UnredisteredObject');
			$this->fail('ModularAuthUtility::unbindObject(UnredisteredObject) %s');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_UnregisteredObjectException');
		}

		try {
			ModularAuthUtility::getObject('UnredisteredObject');
			$this->fail('ModularAuthUtility::getObject(UnredisteredObject) %s');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_UnregisteredObjectException');
		}

		try {
			ModularAuthUtility::deleteObject('UnredisteredObject');
			$this->fail('ModularAuthUtility::deleteObject(UnredisteredObject) %s');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_UnregisteredObjectException');
		}
	}
}