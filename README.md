# ModularAuth #

This is a CakePHP plugin to devour AuthComponent to satisfy your desire for login, authentication, grab user data, additional function and so.

This was made for DRY to develop your auth logic.

## Requirements

PHP > 5.2
CakePHP 1.3

## Version

This was versioned as 0.1 Beta.

## Installation

in your plugins directory,

	git clone git://github.com/hiromi2424/modular_auth.git

or in current directory of your repository,

	git submodule add git://github.com/hiromi2424/modular_auth.git plugins/modular_auth

## Usage

### Create your Authenticator


You can create a component as Authenticator. For example,


	<?php
	
	class AllowDenyComponent extends ModularAuthenticator {
	
		public function beforeStartup(Controller $Controller) {
			if (empty($Controller->params['prefix'])) {
				$this->Auth->allow('*');
			}

			if (!empty($Controller->loginRequired)) {
				$Auth->deny($Controller->loginRequired);
			}
		}
	}


### Specify your Authenticator

You can use above sample Authenticator with ModularAuthComponent. For example,


	class AppController extends Controller {
		public $components = array(
			'ModularAuth.ModularAuth' => array(
				'authenticators' => array(
					'AllowDeny',
				),
			),
		);
	}


This plugin works as same as AuthComponent by default.

## Recommendation

I would recommend to use [HackPlugin](https://github.com/hiromi2424/hack_plugin) to resolve namespace.

### Example


	class AppController extends Controller {
		public $components = array(
			'Hack.Alias' => array(
				'Auth' => array(
					'ModularAuth.ModularAuth' => array(
						'authenticators' => array(
							'AllowDeny',
						),
					),
				)
			),
		);
	}


This allows your controller to be able to access this component as alias for Auth.


	in your controller methods:
	var_dump(get_class($this->Auth)) // will show 'ModularAuthComponent'


## Samples

	class HogeAuthenticatorComponent extends ModularAuthenticator {
		public function beforeLogin($data) {
			if ($this->Controller->RequestHandler->getClientIp(false) === '127.0.0.1') { // you can access your controller with this way
				$this->interrupt(); // you can stop parent method and other authenticator's callback to be called.
				return true;
			}
		
			function afterUser($result) {
				$result = isset($result[$this->Controller->modelClass]) ? $result[$this->Controller->modelClass] : $result;
				$this->overrideResult($result); // you can override result. it is rotated between authenticators after callbacks.
			}
		}
	}


You can specify settings as like:


	class AppController extends Controller {
		public $components = array(
			'ModularAuth.ModularAuth' => array(
				'authenticators' => array(
					'CookieAutoLogin' => array(
						'cookieName' => 'AutoLogin',
					),
				),
			),
		);
	}

You can also take following way to specify authenticators and their settings.

!! Now this is not implemented, but I will implement until stable version released.

	// in your APP/controllers/components/app_modular_auth.php
	class AppModularAuthComponent extends BaseModularAuthComponent {
		public $authenticators = array(
			'CookieAutoLogin' => array(
				'cookieName' => 'AutoLogin',
			),
		);
	}

## License

Licensed under The MIT License.
Redistributions of files must retain the above copyright notice.


Copyright 2010 hiromi, https://github.com/hiromi2424

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.