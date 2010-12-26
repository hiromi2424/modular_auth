<?php

App::import('Lib', 'ModularAuth.ModularAuthUtility');

class ModularAuthUtilityTest extends CakeTestCase {
	function testChangeState() {
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

	function testObjectMethods() {
		$destination = new Object;


		$registered = new Object;
		ModularAuthUtility::registerObject('registered', $registered);
		ModularAuthUtility::bindObject($destination, 'registered');
		$this->assertTrue($destination->Registered === $registered);

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

		ModularAuthUtility::bindObject($destination, array('one', array('two')), array(array(array('three'))));
		$this->assertTrue(isset($destination->One));
		$this->assertTrue(isset($destination->Two));
		$this->assertTrue(isset($destination->Three));

		ModularAuthUtility::unbindObject($destination, 'one', 'two', array(array('three', 'two', 'one')));

		$this->assertFalse(isset($destination->One));
		$this->assertFalse(isset($destination->Two));
		$this->assertFalse(isset($destination->Three));

		$this->assertEqual(array_keys(ModularAuthUtility::registeredObjects()), array('Registered', 'One', 'Two', 'Three'));
		ModularAuthUtility::deleteObject('One');
		$this->assertEqual(array_keys(ModularAuthUtility::registeredObjects()), array('Registered', 'Two', 'Three'));
	}
}