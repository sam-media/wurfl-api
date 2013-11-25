<?php

define("WURFL_DIR", dirname(__FILE__) . '/../WURFL/');
define("RESOURCES_DIR", WURFL_DIR . "../examples/resources/");

require_once dirname(__FILE__) . '/../WURFL/Application.php';

$persistenceDir = RESOURCES_DIR . "storage/persistence";
$wurflConfig = new WURFL_Configuration_InMemoryConfig ();
$wurflConfig->wurflFile(RESOURCES_DIR . "wurfl.zip");
$wurflConfig->persistence("file", array(WURFL_Configuration_Config::DIR => $persistenceDir));
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


function buildPersistenceWith($wurflConfig) {
	$force_rebuild = true;
	$persistenceStorage = WURFL_Storage_Factory::create($wurflConfig->persistence);
	if ($force_rebuild) $persistenceStorage->clear();
	$context = new WURFL_Context($persistenceStorage);
	$userAgentHandlerChain = WURFL_UserAgentHandlerChainFactory::createFrom($context);

	$devicePatcher = new WURFL_Xml_DevicePatcher();
	$deviceRepositoryBuilder = new WURFL_DeviceRepositoryBuilder($persistenceStorage, $userAgentHandlerChain, $devicePatcher);

	return $deviceRepositoryBuilder->build($wurflConfig->wurflFile, $wurflConfig->wurflPatches, $wurflConfig->capabilityFilter);
}



buildPersistenceWith($wurflConfig);

echo "OK";