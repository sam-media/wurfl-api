<?php
/**
 * test case
 */

/**
 * WURFL_CustomDevice test case.
 */
class WURFL_CustomDeviceTest extends PHPUnit_Framework_TestCase {
	
	public function testShouldLaunchExceptionIfPassedArraysDoesNotContainAtLeastOneDevice() {
		try {
			new WURFL_CustomDevice ( array () );
		} catch ( InvalidArgumentException $expected ) {
			return;
		}
		$this->fail ( 'An expected exception has not been raised.' );
	}
	
	public function testShouldTreatNullCapablityValuesAsValidValue() {
		$modelDevice = new WURFL_Xml_ModelDevice ( "parent", "ua", "root", true, false, array ("product_info" => array ("claims_web_support" => NULL ) ) );
		
		$device = new WURFL_CustomDevice ( array ($modelDevice ) );
		$capabilityValue = $device->getCapability ( "claims_web_support" );
		$this->assertEquals ( "", $capabilityValue );
	
	}
	
	public function testShouldLaunchExceptionIfCapabilityNameIsNotDefined() {
		$modelDevice = new WURFL_Xml_ModelDevice ( "parent", "ua", "root", true, false, array ("product_info" => array ("claims_web_support" => "true" ) ) );
		$childModelDevice = new WURFL_Xml_ModelDevice ( "id", "ua", "parent", true, false, array ("product_info" => array ("is_wireless_device" => "true" ) ) );
		
		try {
			$device = new WURFL_CustomDevice ( array ($childModelDevice, $modelDevice ) );
			$device->getCapability ( "inexistent_cap" );
		} catch ( InvalidArgumentException $expected ) {
			return;
		}
		$this->fail ( 'An expected exception has not been raised.' );
	
	}
	
	public function testShoulReturnTheDeviceProperties() {
		$device = new WURFL_CustomDevice ( array ($this->mockModelDevice () ) );
		$this->assertEquals ( $device->id, "parent" );
		$this->assertEquals ( $device->userAgent, "ua" );
		$this->assertEquals ( $device->fallBack, "root" );
		$this->assertEquals ( $device->actualDeviceRoot, true );
	}
	
	public function testShouldLaunchExceptionForInvalidCapabilityName() {
		try {
			$device = new WURFL_CustomDevice ( array ($this->mockModelDevice () ) );
			$device->getCapability ( "" );
		} catch ( InvalidArgumentException $expected ) {
			return;
		}
		$this->fail ( 'An expected exception has not been raised.' );
	}
	
	public function testShouldLaunchExceptionIfCapabilityNameIsNotDefined1() {
		try {
			$device = new WURFL_CustomDevice ( array ($this->mockModelDevice () ) );
			$device->getCapability ( "inexistent" );
		} catch ( InvalidArgumentException $expected ) {
			return;
		}
		$this->fail ( 'An expected exception has not been raised.' );
	
	}
	
	public function testShouldReturnCapabilityDefinedInModelDevice() {
		$modelDevice = new WURFL_Xml_ModelDevice ( "id", "ua", "root", true, false, array ("product_info" => array ("is_wireless_device" => "true" ) ) );
		$device = new WURFL_CustomDevice ( array ($modelDevice ) );
		
		$capabilityValue = $device->getCapability ( "is_wireless_device" );
		$this->assertEquals ( "true", $capabilityValue );
	}
	public function testShouldRetrunCapabilityDefinedInParentModelDevices() {
		$modelDevice = new WURFL_Xml_ModelDevice ( "parent", "ua", "root", true, false, array ("product_info" => array ("claims_web_support" => "false" ) ) );
		$childModelDevice = new WURFL_Xml_ModelDevice ( "id", "ua", "parent", true, false, array ("product_info" => array ("is_wireless_device" => "true" ) ) );
		
		$device = new WURFL_CustomDevice ( array ($childModelDevice, $modelDevice ) );
		$capabilityValue = $device->getCapability ( "claims_web_support" );
		$this->assertEquals ( "false", $capabilityValue );
	
	}
	
	public function testShouldReturnAllCapabilities() {
		$modelDevice = new WURFL_Xml_ModelDevice ( "parent", "ua", "root", true, false, array ("product_info" => array ("claims_web_support" => "false" ) ) );
		$childModelDevice = new WURFL_Xml_ModelDevice ( "id", "ua", "parent", true, false, array ("product_info" => array ("is_wireless_device" => "true" ) ) );
		
		$device = new WURFL_CustomDevice ( array ($childModelDevice, $modelDevice ) );
		$allCapabilities = $device->getAllCapabilities ();
		$this->assertEquals ( $allCapabilities, array ("claims_web_support" => "false", "is_wireless_device" => "true" ) );
	}
	
	private function mockModelDevice() {
		return new WURFL_Xml_ModelDevice ( "parent", "ua", "root", true, false, array ("product_info" => array ("claims_web_support" => "false" ) ) );
	
	}
	
	public function testShouldBeNotSpecificIfHasNotActualDeviceRootInHierarchy() {
		$modelDevices = array (new WURFL_Xml_ModelDevice ( "3", "", "", "", false ), new WURFL_Xml_ModelDevice ( "2", "", "", "", false ), new WURFL_Xml_ModelDevice ( "generic", "", "", "", false ) );
		
		$device = new WURFL_CustomDevice ( $modelDevices );
		$this->assertFalse ( $device->isSpecific () );
	}
	
	public function testShouldBeNotSpecificIfSpecificIsFalse() {
		$modelDevice = new WURFL_Xml_ModelDevice ( "", "", "", "", false );
		$device = new WURFL_CustomDevice ( array ($modelDevice ) );
		$this->assertFalse ( $device->isSpecific () );
	}
	
	public function testShouldBeSpecificIfSpecificIsTrue() {
		$modelDevice = new WURFL_Xml_ModelDevice ( "", "", "", "", true );
		$device = new WURFL_CustomDevice ( array ($modelDevice ) );
		$this->assertTrue ( $device->isSpecific () );
	}
	
	public function testShouldBeSpecificIfHasActualDeviceRootInHierarchy() {
		$modelDevice = new WURFL_Xml_ModelDevice ( "", "", "", "", true );
		$device = new WURFL_CustomDevice ( array ($modelDevice ) );
		$this->assertTrue ( $device->isSpecific () );
	}

}

