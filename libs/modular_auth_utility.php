<?php

App::import('Lib', array(
	'ModularAuth.ModularAuthException',
	'ModularAuth.ModularAuthBaseObject',
), false);

class ModularAuthUtility {
	private static $__stateTable = array(
		false => array(),
		true => array('after', 'before'),
		'before' => array('before'),
		'after' => array('after'),
	);

	public static $authenticators = array();
	protected static $_registeredObjects = array();

	public static function enableState(&$disabled, $modify) {
		return self::_changeState(false, $disabled, $modify);
	}

	public static function disableState(&$disabled, $modify) {
		return self::_changeState(true, $disabled, $modify);
	}

	public static function enabled($disabled, $compare) {
		return !self::_changeState(false, $disabled, $compare);
	}

	public static function disabled($disabled, $compare) {
		return self::_changeState(false, $disabled, $compare);
	}

	protected static function _changeState($off, &$disabled, $modify) {
		$disabledState = self::__state($disabled);
		$modifyState = self::__state($modify);

		$state = $off ? array_values(array_unique(array_merge($disabledState, $modifyState))) : array_diff($disabledState, $modifyState);
		$modify = self::__state($state);
		if ($disabled === $modify) {
			return false;
		}
		$disabled = $modify;
		return true;
	}

	private static function __state($state) {
		if (is_array($state)) {
			sort($state);
			$result = array_search($state, self::$__stateTable);
			return is_numeric($result) ? !!$result : $result;
		}
		return self::$__stateTable[$state];
	}

	public static function loadLibrary($type, $name) {
		$name = self::denormalize($name);
		list($plugin, $objectName) = pluginSplit($name);
		if (in_array(strtolower($type), array('component', 'helper'))) {
			$objectName .= Inflector::camelize($type);
		}

		if (!App::import($type, $name) && !class_exists($objectName)) {
			throw new ModularAuth_ObjectNotFoundException($objectName);
		}
		$object = new $objectName;

		if ($object instanceof ModularAuthBaseObject) {
			self::bindObject($object, 'Controller', 'Auth');
			$object->init();
		}

		return $object;
	}

	public static function loadAuthenticator($name, $type = 'Component') {
		$Authenticator = self::loadLibrary($type, $name);
		self::registerObject($name, $Authenticator);
		return $Authenticator;
	}

	public static function normalize($name) {
		list($plugin, $name) = pluginSplit($name, true);
		return str_replace('.', '_', Inflector::camelize($plugin) . Inflector::camelize($name));
	}

	public static function denormalize($name) {
		list($plugin, $name) = pluginSplit($name);
		if ($plugin) {
			$plugin = Inflector::camelize($plugin) . '.';
			$name = $plugin . Inflector::camelize($name);
		}
		return Inflector::camelize(str_replace('_', '.', $name));
	}

	public static function registerObject($name, $object = null) {
		if (is_array($name)) {
			foreach ($name as $n => $object) {
				self::registerObject($n, $object);
			}
			return;
		}

		self::$_registeredObjects[self::normalize($name)] = $object;
		if ($object instanceof ModularAuthenticator) {
			self::bindObject('Auth', $name);
			self::$authenticators[] = self::normalize($name);
		}
	}

	public static function getObject($name) {
		$name = self::normalize($name);

		if (!isset(self::$_registeredObjects[$name])) {
			throw new ModularAuth_UnregisteredObjectException($name);
		}
		return self::$_registeredObjects[$name];
	}

	public static function deleteObject($name) {
		if (is_array($name)) {
			foreach ($name as $n) {
				self::deleteObject($n);
			}
			return;
		}

		$name = self::normalize($name);
		if (!isset(self::$_registeredObjects[$name])) {
			throw new ModularAuth_UnregisteredObjectException($name);
		}
		if (false !== ($index = array_search($name, self::$authenticators))) {
			self::unbindObject('Auth', $name);
			unset(self::$authenticators[$index]);
		}
		unset(self::$_registeredObjects[$name]);
	}

	public static function flushObjects() {
		if (empty(self::$_registeredObjects)) {
			return false;
		}
		self::deleteObject(self::$authenticators);
		self::deleteObject(array_keys(self::$_registeredObjects));
		return true;
	}

	public static function bindObject($destination, $names) {
		list($destination, $names) = self::__normalizeArguments(func_get_args());

		foreach ((array)$names as $name) {
			$name = self::normalize($name);
			if (!isset(self::$_registeredObjects[$name])) {
				throw new ModularAuth_UnregisteredObjectException($name);
			}
			$destination->$name = self::$_registeredObjects[$name];
		}
	}

	public static function unbindObject($destination, $names) {
		list($destination, $names) = self::__normalizeArguments(func_get_args());

		foreach ((array)$names as $name) {
			$name = self::normalize($name);
			if (!isset(self::$_registeredObjects[$name])) {
				throw new ModularAuth_UnregisteredObjectException($name);
			}
			unset($destination->$name);
		}
	}

	public static function isRegistered() {
		$names = Set::flatten(func_get_args());

		foreach ((array)$names as $name) {
			if (is_string($name)) {
				$name = self::normalize($name);
				if (!isset(self::$_registeredObjects[$name])) {
					return false;
				}
			} elseif(is_object($name)) {
				if (!in_array($name, self::$_registeredObjects)) {
					return false;
				}
			} else {
				return null;
			}
		}
		return true;
	}

	private static function __normalizeArguments($args) {
		$destination = array_shift($args);
		if (!is_object($destination)) {
			$destination = self::getObject($destination);
		}
		return array($destination, Set::flatten($args));
	}

}