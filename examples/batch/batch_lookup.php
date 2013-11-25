<?php
/*
 * This example takes user agents from standard in and
 * outputs those same user agents to stadard out with 
 * a tab char, then the detected WURFL device id.
 * 
 * It is optimized for high performance batch processes.
 * Note that each time this script is run it will load the
 * WURFL file, so there will be a ~15 sec delay before the
 * user agents start being detected.  Caching is not used
 * because in most environments the cost of caching is
 * higher than the cost of doing in-memory analysis
 */

function echo_stderr($message) {
	fputs(STDERR, $message);
}

$wurflDir = dirname(__FILE__) . '/../../../WURFL';
$resourcesDir = dirname(__FILE__) . '/../../resources';

require_once "$wurflDir/Application.php";
require_once "$wurflDir/Configuration/InMemoryConfig.php";

$wurflConfig = new WURFL_Configuration_InMemoryConfig();
$wurflConfig->wurflFile("$resourcesDir/wurfl.zip")
	->persistence("memory")
	->cache("null")
	->allowReload(true)
	// 'performance' or 'accuracy'
	->matchMode('performance');

echo_stderr("Initializing WURFL...");
$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
$wurflManager = $wurflManagerFactory->create();
echo_stderr("done\n\n");

$i = -1;
while (!feof(STDIN)) {
	$i++;
	$ua = trim(fgets(STDIN, 4096));
	$device = $wurflManager->getDeviceForUserAgent($ua);
	echo "$ua\t{$device->id}\n";
}