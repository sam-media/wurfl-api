<?php
/**
 * test case
 */

class WURFL_Xml_DevicePatcherTest extends PHPUnit_Framework_TestCase {

	private $devicePatcher;
	
	public function setUp() {
		$this->devicePatcher = new WURFL_Xml_DevicePatcher();
	}
	
	
	public function testShouldReturnThePatchingDeviceIfForDifferentDevices() {
		$deviceToPatch = new WURFL_Xml_ModelDevice("A", "A", "Z");
		$patchingDevice = new WURFL_Xml_ModelDevice("B", "B", "Z");
		
		$patchedDevice = $this->devicePatcher->patch($deviceToPatch, $patchingDevice);
		$this->assertEquals($patchingDevice, $patchedDevice);
	}	

	
	public function testShouldOverrideTheCapabilities() {
		$deviceToPatch = new WURFL_Xml_ModelDevice("A", "A", "Z", true, false,array());		
		$groupIDCapabilitiesMap=array();
		$groupIDCapabilitiesMap ["A"] ["cap1"] = "cap1";
		$capabilities = array();
		$capabilities["cap1"] = "cap1";		
		$patchingDevice = new WURFL_Xml_ModelDevice("B", "B", "Z", true, false, $groupIDCapabilitiesMap);
		$patchedDevice = $this->devicePatcher->patch($deviceToPatch, $patchingDevice);
		
		$this->assertEquals($capabilities, $patchedDevice->capabilities);
		
	}
	
	
	
	public function testShouldOnlyOverrideTheCapabilitiesSpecifiedByThePatcherDevices() {
		$groupIDCapabilitiesMap=array();
		$groupIDCapabilitiesMap ["A"] ["cap1"] = "cap1";
		$groupIDCapabilitiesMap ["A"] ["cap2"] = "cap2";
		
		$deviceToPatch = new WURFL_Xml_ModelDevice("A", "A", "Z", true, false, $groupIDCapabilitiesMap);		
		
		$groupIDCapabilitiesMap=array();
		$groupIDCapabilitiesMap ["A"] ["cap1"] = "cap1";
		$groupIDCapabilitiesMap ["A"] ["cap3"] = "cap3";
		
		$capabilities = array();
		$capabilities["cap1"] = "cap1";
		$capabilities["cap2"] = "cap2";
		$capabilities["cap3"] = "cap3";
		
								
		$patchingDevice = new WURFL_Xml_ModelDevice("A", "A", "Z", true, false, $groupIDCapabilitiesMap);
		$patchedDevice = $this->devicePatcher->patch($deviceToPatch, $patchingDevice);
		$this->assertEquals($capabilities, $patchedDevice->capabilities);
	}

}

