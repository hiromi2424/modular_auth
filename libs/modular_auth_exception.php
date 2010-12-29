<?php

class ModularAuthException extends Exception {
	
}

class ModularAuth_ObjectNotFoundException extends ModularAuthException {}
class ModularAuth_UnregisteredObjectException extends ModularAuthException {}
class ModularAuth_AuthenticatorNotFoundException extends ModularAuthException {}
class ModularAuth_IllegalAuthenticatorMethodException extends ModularAuthException {}
class ModularAuth_IllegalAuthenticatorObjectException extends ModularAuthException {}
