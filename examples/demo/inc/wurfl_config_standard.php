<?php
/*
 * This is an example of configuring the WURFL PHP API
 */

// Enable all error logging while in development
ini_set('display_errors', 'on');
error_reporting(E_ALL);

$wurflDir = dirname(__FILE__) . '/../../../WURFL';
$resourcesDir = dirname(__FILE__) . '/../../resources';

require_once $wurflDir.'/Application.php';

$persistenceDir = $resourcesDir.'/storage/persistence';
$cacheDir = $resourcesDir.'/storage/cache';

// Create WURFL Configuration
$wurflConfig = new WURFL_Configuration_InMemoryConfig();

// Set location of the WURFL File
$wurflConfig->wurflFile($resourcesDir.'/wurfl.zip');

// Set the match mode for the API ('performance' or 'accuracy')
$wurflConfig->matchMode('performance');

// Automatically reload the WURFL data if it changes
$wurflConfig->allowReload(true);

/*
// Optionally specify which capabilities should be loaded
//  This is disabled by default as it would cause the demo/index.php
//  page to fail due to missing capabilities
$wurflConfig->capabilityFilter(array(
	'is_wireless_device',
	'preferred_markup',
	'xhtml_support_level',
	'xhtmlmp_preferred_mime_type',
	'device_os',
	'device_os_version',
	'is_tablet',
	'mobile_browser_version',
	'pointing_method',
	'mobile_browser',
	'resolution_width',
));
*/

// Setup WURFL Persistence
$wurflConfig->persistence('file', array('dir' => $persistenceDir));

// Setup Caching
$wurflConfig->cache('file', array('dir' => $cacheDir, 'expiration' => 36000));

// Create a WURFL Manager Factory from the WURFL Configuration
$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

// Create a WURFL Manager
/* @var $wurflManager WURFL_WURFLManager */
$wurflManager = $wurflManagerFactory->create();
