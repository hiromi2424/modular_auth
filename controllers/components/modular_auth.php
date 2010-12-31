<?php

App::import('Component', 'ModularAuth.BaseModularAuth');

if (!App::import('Component', 'AppModularAuth')) {
	App::import('Component', 'ModularAuth.AppModularAuth');
}

class ModularAuthComponent extends AppModularAuthComponent {
}
