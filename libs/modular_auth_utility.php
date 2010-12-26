<?php

App::import('Lib', 'ModularAuth.ModularAuthException', false);

class ModularAuthUtility {
	private static $__stateTable = array(
		false => array(),
		true => array('after', 'before'),
		'before' => array('before'),
		'after' => array('after'),
	);

	protected static $_registeredObjects = array();

	public static function enableState(&$property, $modify) {
		return self::_changeState(false, $property, $modify);
	}

	public static function disableState(&$property, $modify) {
		return self::_changeState(true, $property, $modify);
	}

	protected static function _changeState($off, &$property, $modify) {
		$propertyState = self::__state($property);
		$modifyState = self::__state($modify);

		$state = $off ? array_values(array_unique(array_merge($propertyState, $modifyState))) : array_diff($propertyState, $modifyState);
		$propertyState = self::__state($state);
		if ($property === $propertyState) {
			return false;
		}
		$property = $propertyState;
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
		$name = Inflector::camelize(self::denormalize($name));
		list($plugin, $objectName) = pluginSplit($name);
		if (in_array(strtolower($type), array('component', 'helper'))) {
			$objectName .= Inflector::camelize($type);
		}

		if (!App::import($type, $name) && !class_exists($objectName)) {
			throw new ModularAuth_ObjectNotFoundException;
		}
		$object = new $objectName;

		if ($object instanceof ModularAuthenticator || $object instanceof ModularAuthenticators) {
			self::bindObject($object, 'Controller', 'Auth');
		}

		if ($object instanceof ModularAuthenticator) {
			self::registerObject(self::normalize($name), $object);
		}

		return $object;
	}

	public static function loadAuthenticator($name) {
		
	}

	public static function normalize($name) {
		return str_replace('.', '_', $name);
	}

	public static function denormalize($name) {
		return str_replace('_', '.', $name);
	}

	public static function registerObject($name, $object = null) {
		if (is_array($name)) {
			foreach ($name as $n => $object) {
				self::registerObject($n, $object);
			}
			return;
		}

		self::$_registeredObjects[Inflector::camelize($name)] = $object;
	}

	public static function deleteObject($name) {
		if (is_array($name)) {
			foreach ($name as $n) {
				self::deleteObject($n);
			}
			return;
		}

		$name = Inflector::camelize($name);
		if (!isset(self::$_registeredObjects[$name])) {
			throw new ModularAuth_UnregisteredObjectException;
		}
		unset(self::$_registeredObjects[$name]);
	}

	public static function bindObject($destination, $names) {
		$names = self::__normalizeArguments(func_get_args());

		foreach ((array)$names as $name) {
			$name = Inflector::camelize($name);
			if (!isset(self::$_registeredObjects[$name])) {
				throw new ModularAuth_UnregisteredObjectException;
			}
			$destination->$name = self::$_registeredObjects[$name];
		}
	}

	public static function unbindObject($destination, $names) {
		$names = self::__normalizeArguments(func_get_args());

		foreach ((array)$names as $name) {
			$name = Inflector::camelize($name);
			if (!isset(self::$_registeredObjects[$name])) {
				throw new ModularAuth_UnregisteredObjectException;
			}
			unset($destination->$name);
		}
	}

	private static function __normalizeArguments($args) {
		array_shift($args);
		return array_values(Set::flatten($args));
	}

	// for debug, test
	public static function registeredObjects() {
		return self::$_registeredObjects;
	}
}