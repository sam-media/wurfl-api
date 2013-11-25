<?php

// Enable all error logging while in development
ini_set('display_errors', 'on');
error_reporting(E_ALL);

$wurflDir = dirname(__FILE__) . '/../../WURFL';
$resourcesDir = dirname(__FILE__) . '/../resources';
require_once $wurflDir.'/Application.php';

$persistenceDir = $resourcesDir.'/storage/persistence';
$cacheDir = $resourcesDir.'/storage/cache';

$wurflConfig = new WURFL_Configuration_InMemoryConfig();
$wurflConfig->wurflFile($resourcesDir.'/wurfl.zip');
$wurflConfig->matchMode('performance');
$wurflConfig->persistence('file', array('dir' => $persistenceDir));
$wurflConfig->cache('file', array('dir' => $cacheDir, 'expiration' => 36000));
$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
$wurflManager = $wurflManagerFactory->create();

$wurflInfo = $wurflManager->getWURFLInfo();

define("XHTML_ADVANCED", "xhtml_advanced.php");
define("XHTML_SIMPLE", "xhtml_simple.php");
define("WML", "wml.php");

$device = $wurflManager->getDeviceForHttpRequest($_SERVER);

$xhtml_lvl = $device->getCapability('xhtml_support_level');
$contentType = $device->getCapability('xhtmlmp_preferred_mime_type');

$page = getPageFromMarkup($xhtml_lvl);
renderPage($page, $contentType);

function getPageFromMarkup($xhtml_lvl) {
	/* xhtml_support_level possible values:
	 * -1: No XHTML Support
	 *  0: Poor XHTML Support
	 *  1: Basic XHTML with Basic CSS Support
	 *  2: Same as Level 1
	 *  3: XHTML Support with Excellent CSS Support
	 *  4: Level 3 + AJAX Support
	 */
	if ($xhtml_lvl < 1) {
		return WML;
	}
	if ($xhtml_lvl < 3) {
		return XHTML_SIMPLE;
	}
	return XHTML_ADVANCED;
}

function renderPage($page, $contentType) {
	header("Content-Type: $contentType");
	header("Vary: User-Agent");
	include dirname(__FILE__).'/'.$page;
	exit;
}