<?php
/**
 * Copyright (c) 2014 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING.txt file distributed with this package.
 *
 * @category   WURFL
 * @package	WURFL
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
/**
 * Helper to load PHP classes on demand
 * @package	WURFL
 */
class WURFL_ClassLoader {
	
	const CLASS_PREFIX = "WURFL_";
	private static $classPath;
	/**
	 * Loads a Class given the class name
	 *
	 * @param string $className
	 * @return boolean success
	 */
	public static function loadClass($className) {
		if($className === null) {
			throw new WURFL_WURFLException("Unable To Load Class : " . $className);
		}
		if (substr($className, 0, 6) !== WURFL_ClassLoader::CLASS_PREFIX) {
			return false;
		}
		if (!class_exists($className, false)) {
			if (self::$classPath === null) self::$classPath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
			$classFilePath = str_replace('_', DIRECTORY_SEPARATOR, substr($className, 6)) . '.php';
			include self::$classPath.$classFilePath;
		}
		return false;
	}
}

// register class loader
spl_autoload_register(array('WURFL_ClassLoader', 'loadClass'));
