<?php

App::uses('BaseModularAuthComponent', 'ModularAuth.Controller/Component');

App::uses('AppModularAuthComponent', 'Component');
if (!class_exists('AppModularAuthComponent')) {
	App::uses('AppModularAuthComponent', 'ModularAuth.Controller/Component');
}

class ModularAuthComponent extends AppModularAuthComponent {
}
