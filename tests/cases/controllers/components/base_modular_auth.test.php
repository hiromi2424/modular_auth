<?php

App::import('Lib', 'ModularAuth.ModularAuthTestCase', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

class BaseModularAuthComponentTestCase extends ModularAuthTestCase {
	function startTest() {
		$this->Component = new MockBaseModularAuthComponent;
	}

	function testCallParent() {
		$this->assertEqual($this->Component->callParent('password', array('hoge')), Security::hash('hoge', null, true));
		$this->expectException('ModularAuth_IllegalAuthComponentMethodException');
		$this->Component->callParent('undefined_auth_component_method', array('fuga'));
	}
}
