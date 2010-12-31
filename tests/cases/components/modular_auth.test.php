<?php

App::import('Lib', 'ModularAuth.ModularAuthTestCase', false, array(App::pluginPath('ModularAuth') . 'tests' . DS . 'lib'));

class ModularAuthComponentTestCase extends ModularAuthTestCase {
	function startTest() {
		$this->Component = new ModularAuthComponent;
	}
}
