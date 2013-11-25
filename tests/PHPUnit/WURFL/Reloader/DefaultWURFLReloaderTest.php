<?php
/**
 * test case
 */
/**
 *  test case.
 */
class WURFL_Reloader_DefaultWURFLReloaderTest extends PHPUnit_Framework_TestCase {
	
	const WURFL_CONFIG_FILE = "../../../resources/wurfl-config-reloading.xml";
	
	protected function setUp() {
		parent::setUp ();
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException 
	 */
	public function shoudLaunchExceptionForInvalidConfigurationFilePath() {
		$configurationFilePath = "";
		$wurflReloader = new WURFL_Reloader_DefaultWURFLReloader();
		$wurflReloader->reload($configurationFilePath);	
	}
	
	
	/**
	 * @test
	 *
	 */
	public function shoudReloadTheWurfl() {
		$configurationFilePath = dirname(__FILE__) . DIRECTORY_SEPARATOR .  self::WURFL_CONFIG_FILE;
		$wurflReloader = new WURFL_Reloader_DefaultWURFLReloader();
		$wurflReloader->reload($configurationFilePath);
	}
	
	protected function tearDown() {
		parent::tearDown ();
	}


}

