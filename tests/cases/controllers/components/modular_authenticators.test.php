<?php

App::import('Lib', 'ModularAuth.ModularAuthTestCase', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

class ModularAuthenticatorsComponentTestCase extends ModularAuthTestCase {

	function startTest($method) {

		parent::startTest($method);

		ModularAuthUtility::registerObject('Auth', $this->Auth);
		ModularAuthUtility::registerObject('Controller', $this->Controller);
		$this->Authenticators = ModularAuthUtility::loadLibrary('Component', 'ModularAuthenticators');
		$this->Auth->Authenticators = $this->Authenticators;

	}

	public function testObjectMethod() {

		$this->Authenticators->append('FirstMockModularAuthenticator');
		$this->assertTrue($this->Authenticators->exists('FirstMockModularAuthenticator'));
		$this->assertIsA($this->Authenticators->get('FirstMockModularAuthenticator'), 'FirstMockModularAuthenticatorComponent');

		$this->Authenticators->append('FirstMockModularAuthenticator', array('disable' => true));
		$this->assertTrue($this->Authenticators->get('FirstMockModularAuthenticator')->disabled());

		$this->Authenticators->drop('FirstMockModularAuthenticator');
		$this->assertFalse($this->Authenticators->exists('FirstMockModularAuthenticator'));

		$this->Authenticators->append(array('FirstMockModularAuthenticator', 'SecondMockModularAuthenticator'));
		$this->assertTrue($this->Authenticators->exists('FirstMockModularAuthenticator', 'SecondMockModularAuthenticatorComponent'));

		ModularAuthUtility::deleteObject('FirstMockModularAuthenticator', 'SecondMockModularAuthenticatorComponent');
		$this->Authenticators->append('Hoge', new FirstMockModularAuthenticatorComponent($this->Collection, array()));
		$this->assertTrue($this->Authenticators->exists('Hoge'));

		try {
			$this->Authenticators->append('HogeModularAuthenticator');
			$this->fail('Unavailable object');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_AuthenticatorNotFoundException');
		}

		try {
			$this->Authenticators->append(null);
			$this->fail('empty name');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_IllegalAuthenticatorNameException');
			$this->assertIdentical($e->getMessage(), 'NULL');
		}

		$this->expectException('ModularAuth_IllegalAuthenticatorObjectException');
		$this->Authenticators->append('Hoge', new Object);
	}

	public function testMagick() {

		$this->Authenticators->FirstMockModularAuthenticator = null;
		$this->assertTrue(isset($this->Authenticators->FirstMockModularAuthenticator));
		$this->assertIsA($this->Authenticators->FirstMockModularAuthenticator, 'FirstMockModularAuthenticatorComponent');
		unset($this->Authenticators->FirstMockModularAuthenticator);
		$this->assertFalse(isset($this->Authenticators->FirstMockModularAuthenticator));

	}

	public function testArrayAccess() {

		$this->Authenticators['FirstMockModularAuthenticator'] = null;
		$this->assertTrue(isset($this->Authenticators['FirstMockModularAuthenticator']));
		$this->assertIsA($this->Authenticators['FirstMockModularAuthenticator'], 'FirstMockModularAuthenticatorComponent');
		unset($this->Authenticators['FirstMockModularAuthenticator']);
		$this->assertFalse(isset($this->Authenticators['FirstMockModularAuthenticator']));

	}

	public function testTriggerCallback() {

		$this->Authenticators->append('SecondMockModularAuthenticator');

		$this->assertNull($this->Authenticators->triggerCallback('before', 'logout', array(), false));
		$this->assertEqual($this->Authenticators->triggerCallback('before', 'logout', array(), 'array'), array('SecondMockModularAuthenticator' => true));
		$this->assertTrue($this->Authenticators->triggerCallback('before', 'logout', array()));

		$this->Authenticators->disable();

		$this->assertTrue($this->Authenticators->triggerCallback('before', 'logout', array()));

		$this->Authenticators->enable();

		$this->Authenticators->SecondMockModularAuthenticator->disable();
		$this->assertTrue($this->Authenticators->triggerCallback('before', 'logout', array()));
		$this->assertEqual($this->Authenticators->triggerCallback('before', 'logout', array(), 'array'), array('SecondMockModularAuthenticator' => true));
		$this->Authenticators->SecondMockModularAuthenticator->enable();

		$this->Authenticators->drop('SecondMockModularAuthenticator');
		$this->Authenticators->append('FirstMockModularAuthenticator');
		$this->Authenticators->append('SecondMockModularAuthenticator');

		$this->assertEqual($this->Authenticators->triggerCallback('after', 'logout', array('result'), 'enchain'), 'piyo');
		$this->assertEqual($this->Authenticators->triggerCallback('before', 'logout', array()), false);

		$this->Authenticators->drop('FirstMockModularAuthenticator');
		$this->Authenticators->drop('SecondMockModularAuthenticator');

		try {
			$this->Authenticators->triggerCallback('after', 'logout', array('result'), 'undefined return type');
			$this->fail('undefined return type');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_IllegalArgumentException');
			$this->assertEqual($e->getMessage(), '$return = undefined return type');
		}

		$this->Authenticators->append('FirstMockModularAuthenticator');
		try {
			$this->Authenticators->triggerCallback('after', 'logout', array('result'), 'undefined return type');
			$this->fail('undefined return type');
		} catch (Exception $e) {
			$this->assertIsA($e, 'ModularAuth_IllegalArgumentException');
			$this->assertEqual($e->getMessage(), '$return = undefined return type');
		}

	}

	function testInterruption() {

		$this->Authenticators->append('MockModularAuthenticator');
		$this->assertEqual($this->Authenticators->triggerCallback('before', 'login', array()), 'interrupted');

	}
}