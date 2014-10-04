<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(dirname(dirname(__FILE__))));

require_once 'WURFL/ClassLoader.php';
require_once 'tests/PHPUnit/WURFL/TestUtils/MockPersistenceProvider.php';
require_once 'tests/PHPUnit/WURFL/TestUtils.php';
// register class loader
spl_autoload_register ( array ('WURFL_ClassLoader', 'loadClass' ) );
