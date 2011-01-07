<?php

class ModularAuthException extends Exception {
	public function __construct($message = null, $code = 0, Exception $previous = null) {
		if (Configure::read('ModularAuth.debug_trace')) {
			$trace = "\n" . Debugger::trace(array('start' => 1));
			var_dump($trace);
		}
		parent::__construct($message, $code, $previous);
	}
}

class ModularAuth_ObjectNotFoundException extends ModularAuthException {}
class ModularAuth_UnregisteredObjectException extends ModularAuthException {}
class ModularAuth_AuthenticatorNotFoundException extends ModularAuthException {}
class ModularAuth_IllegalAuthComponentMethodException extends ModularAuthException {}
class ModularAuth_IllegalAuthenticatorMethodException extends ModularAuthException {}
class ModularAuth_IllegalAuthenticatorObjectException extends ModularAuthException {}
class ModularAuth_IllegalArgumentsException extends ModularAuthException {}
class ModularAuth_IllegalArgumentException extends ModularAuthException {}