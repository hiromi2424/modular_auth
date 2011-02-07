<?php

App::import('Lib', 'ModularAuth.ModularAuthTestCase', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

class ModularAuthBaseObjectTestCase extends ModularAuthTestCase {

	public function startTest($method) {

		parent::startTest($method);
		$this->TestObject = $this->getMock('ModularAuthBaseObject', null, array($this->Collection, array()));

	}

	public function testInit() {

		$this->assertNull($this->TestObject->init());

	}

	public function testStateMethods() {

		$this->assertTrue($this->TestObject->enabled(), 'enabled at first');

		$this->assertTrue($this->TestObject->disable(), 'disable success');

		$this->assertTrue($this->TestObject->disabled(), 'object was disabled');
		$this->assertFalse($this->TestObject->enabled(), 'object was disabled so it is not enabled');
		$this->assertFalse($this->TestObject->enabled('before'), 'specifying call back also can make sure');

		$this->assertFalse($this->TestObject->disable(), 'double disable failed');
		$this->assertTrue($this->TestObject->enable(), 'enable success on it was disabled');


		$this->assertTrue($this->TestObject->disable('before'), 'disable before callback success');

		$this->assertTrue($this->TestObject->disabled(), 'was it disabled either after or berore or all?');
		$this->assertTrue($this->TestObject->disabled('before'), 'before callback was disabled');
		$this->assertFalse($this->TestObject->disabled('after'), 'after callback was not disabled');
		$this->assertTrue($this->TestObject->enabled('after'), 'after callback is enabled since before callback was disabled');

		$this->assertFalse($this->TestObject->enable('after'), 'after callback is already enabled');

		$this->assertTrue($this->TestObject->enable('before'), 'enable before callback success beacuse it was disabled');
		$this->assertFalse($this->TestObject->disabled(), 'object is not disabled because before callback was enabled');


		$this->assertTrue($this->TestObject->disableMethod('startup'), 'disable startup method');

		$this->assertTrue($this->TestObject->methodDisabled('startup'), 'startup method is disabled');
		$this->assertFalse($this->TestObject->methodEnabled('startup'), 'startup method is not enabled');
		$this->assertFalse($this->TestObject->enable(), 'startup method is disabled, but object is exactly enabled so enable() failed');
		$this->assertFalse($this->TestObject->methodEnabled('startup'), 'startup method is exactly not enabled after enable()');

		$this->assertTrue($this->TestObject->enableMethod('startup'), 'enable startup method success since it was disabled');

		$this->assertTrue($this->TestObject->methodEnabled('startup'), 'startup method is enabled after enable it');


		$this->assertTrue($this->TestObject->disableMethod('all'), 'disable with specifying "all" success');
		$this->assertTrue($this->TestObject->methodDisabled('all'), 'all method was disabled');
		$this->assertTrue($this->TestObject->methodDisabled('startup'), 'startup method is also disabled');
		$this->assertTrue($this->TestObject->disabled(), 'object was disabled because all method was disabled');
		$this->assertTrue($this->TestObject->enable(), 'enable object success because disabling all method means disabling object');


		$this->assertTrue($this->TestObject->disableMethod('startup', 'before'), 'disable before callback of statup method success');

		$this->assertTrue($this->TestObject->methodDisabled('startup'), 'both or (either before or after) callback of startup method was disabled');
		$this->assertFalse($this->TestObject->methodDisabled('startup', 'after'), 'after callback of startup method is not disabled');
		$this->assertTrue($this->TestObject->enabled(), 'object is enabled since before callback of startup method was disabled');
		$this->assertTrue($this->TestObject->methodDisabled('startup', 'before'), 'before callback of startup method is exactly disabled');

		$this->expectException('ModularAuth_IllegalArgumentsException', null, 'Exception was thrown when too many arguments specified');
		$this->TestObject->enable(1, 2, 3);

	}

	public function testConfigure() {

		$this->TestObject->configure(null);
		$this->assertTrue($this->TestObject->enabled(), 'null can be handled');

		$this->TestObject->configure(array());
		$this->assertTrue($this->TestObject->enabled(), 'nothing happen with empty array()');


		$this->TestObject->configure(array('disable' => 'before'));
		$this->assertTrue($this->TestObject->disabled('before'), 'specifying disable before success');

		$this->TestObject->configure(array('enable' => 'before'));
		$this->assertTrue($this->TestObject->enabled('before'), 'specifying enable before success');


		$this->TestObject->configure(array('disableMethods' => 'login'));
		$this->assertTrue($this->TestObject->methodDisabled('login'), 'specifying disable login method success');
		$this->TestObject->configure(array('enableMethods' => 'login'));
		$this->assertTrue($this->TestObject->methodEnabled('login'), 'specifying enable login method success');

	}

}
