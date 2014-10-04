<?php
$classLoaderDir =  dirname(__FILE__) . DIRECTORY_SEPARATOR . '../../WURFL/ClassLoader.php';

require_once $classLoaderDir;
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'WURFL/TestUtils/MockPersistenceProvider.php';
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'WURFL/TestUtils.php';
// register class loader
spl_autoload_register ( array ('WURFL_ClassLoader', 'loadClass' ) );
