<?php
/**
 * test case
 */

/**
 * WURFL_CustomDevice test case.
 */
class WURFL_VirtualCapabilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var WURFL_WURFLManager
     */
    protected static $wurfl;

    public static function setUpBeforeClass()
    {
        // Create WURFL Configuration
        $wurflConfig = new WURFL_Configuration_InMemoryConfig();

        // Set location of the WURFL File
        $wurflConfig->wurflFile(__DIR__ . '/../../../../examples/resources/wurfl.zip');

        // Setup WURFL Persistence
        $wurflConfig->persistence('memory');

        // Setup Caching
        $wurflConfig->cache('null');

        // Create a WURFL Manager Factory from the WURFL Configuration
        $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

        // Create a WURFL Manager
        self::$wurfl = $wurflManagerFactory->create();
    }

    public static function tearDownAfterClass()
    {
        self::$wurfl = null;
    }

    public function testShouldNotContainControlCaps()
    {
        $device = self::$wurfl->getDevice('generic');
        foreach ($device->getAllCapabilities() as $cap => $value) {
            $this->assertNotContains('controlcap_', $cap);
        }
    }

    public function testShouldReturnForcedValue()
    {
        $device = self::$wurfl->getDevice('generic_smarttv_chromecast');
        $this->assertEquals('Chromecast', $device->getVirtualCapability('advertised_device_os'));
        $this->assertEquals('Chromecast', $device->getCapability('controlcap_advertised_device_os'));

        $device = self::$wurfl->getDevice('android_gce_ver1');
        $this->assertTrue($device->getVirtualCapability('is_robot'));
        $this->assertEquals('force_true', $device->getCapability('controlcap_is_robot'));
    }
}
