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
		$name = self::denormalize($name);
		list($plugin, $objectName) = pluginSplit($name);
		if (in_array(strtolower($type), array('component', 'helper'))) {
			$objectName .= Inflector::camelize($type);
		}

		if (!App::import($type, $name) && !class_exists($objectName)) {
			throw new ModularAuth_ObjectNotFoundException($objectName);
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

	public static function loadAuthenticator($name, $type = 'Component') {
		return self::loadLibrary($type, $name);
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
	}

	public static function getObject($name) {
		$name = self::normalize($name);

		if (!isset(self::$_registeredObjects[$name])) {
			throw new ModularAuth_UnregisteredObjectException;
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
			throw new ModularAuth_UnregisteredObjectException;
		}
		unset(self::$_registeredObjects[$name]);
	}

	public static function flushObjects() {
		if (empty(self::$_registeredObjects)) {
			return false;
		}
		self::$_registeredObjects = array();
		return true;
	}

	public static function bindObject($destination, $names) {
		$names = self::__normalizeArguments(func_get_args());

		foreach ((array)$names as $name) {
			$name = self::normalize($name);
			if (!isset(self::$_registeredObjects[$name])) {
				throw new ModularAuth_UnregisteredObjectException;
			}
			$destination->$name = self::$_registeredObjects[$name];
		}
	}

	public static function unbindObject($destination, $names) {
		$names = self::__normalizeArguments(func_get_args());

		foreach ((array)$names as $name) {
			$name = self::normalize($name);
			if (!isset(self::$_registeredObjects[$name])) {
				throw new ModularAuth_UnregisteredObjectException;
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
		array_shift($args);
		return Set::flatten($args);
	}

	// for debug, test
	public static function registeredObjects() {
		return self::$_registeredObjects;
	}

}