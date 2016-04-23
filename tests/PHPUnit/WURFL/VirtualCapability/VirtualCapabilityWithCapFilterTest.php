<?php
/**
 * test case
 */
class WURFL_VirtualCapabilityWithCapFilterTest extends PHPUnit_Framework_TestCase
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

        $wurflConfig->wurflPatch(__DIR__ . '/../../../resources/patch-controlcaps.xml');

        // Setup WURFL Persistence
        $wurflConfig->persistence('memory');

        // Setup Caching
        $wurflConfig->cache('null');

        $wurflConfig->capabilityFilter(array(
            'device_os',
            'device_os_version',
            'is_tablet',
            'is_wireless_device',
            'pointing_method',
            'preferred_markup',
            'resolution_height',
            'resolution_width',
            'ux_full_desktop',
            'xhtml_support_level',
            'is_smarttv',
            'can_assign_phone_number',
            'brand_name',
            'model_name',
            'marketing_name',
            'mobile_browser_version',
        ));

        $wurflConfig->wurflPatch(__DIR__ . '/../../../resources/patch-controlcaps.xml');

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

    public function testVirtualCapsPatchedDevice()
    {
        $device = self::$wurfl->getDevice('jojo_phone');
        $this->assertEquals(1890, $device->getCapability('resolution_width'));

        $this->assertEquals('Brando One', $device->getVirtualCapability('device_name'));
        $this->assertEquals('Brando One', $device->getCapability('controlcap_device_name'));

        $this->assertTrue($device->getVirtualCapability('is_smartphone'));
        $this->assertEquals('force_true', $device->getCapability('controlcap_is_smartphone'));

        $this->assertFalse($device->getVirtualCapability('is_app'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_app'));

        $this->assertTrue($device->getVirtualCapability('is_mobile'));
        $this->assertEquals('force_true', $device->getCapability('controlcap_is_mobile'));

        $this->assertFalse($device->getVirtualCapability('is_full_desktop'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_full_desktop'));

        $this->assertFalse($device->getVirtualCapability('is_robot'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_robot'));

        $this->assertEquals('LuckyLaondOS', $device->getVirtualCapability('advertised_device_os'));
        $this->assertEquals('LuckyLaondOS', $device->getCapability('controlcap_advertised_device_os'));

        $this->assertFalse($device->getVirtualCapability('is_app_webview'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_app_webview'));

        $this->assertTrue($device->getVirtualCapability('is_phone'));
        $this->assertEquals('force_true', $device->getCapability('controlcap_is_phone'));

        $this->assertEquals('Phantom', $device->getVirtualCapability('advertised_browser'));
        $this->assertEquals('Phantom', $device->getCapability('controlcap_advertised_browser'));

        $this->assertEquals('Lucky land Jojophone Brando-One', $device->getVirtualCapability('complete_device_name'));
        $this->assertEquals('Lucky land Jojophone Brando-One', $device->getCapability('controlcap_complete_device_name'));

        $this->assertTrue($device->getVirtualCapability('is_largescreen'));
        $this->assertEquals('force_true', $device->getCapability('controlcap_is_largescreen'));

        $this->assertFalse($device->getVirtualCapability('is_android'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_android'));

        $this->assertFalse($device->getVirtualCapability('is_xhtmlmp_preferred'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_xhtmlmp_preferred'));

        $this->assertTrue($device->getVirtualCapability('is_html_preferred'));
        $this->assertEquals('force_true', $device->getCapability('controlcap_is_html_preferred'));

        $this->assertEquals('1.1', $device->getVirtualCapability('advertised_browser_version'));
        $this->assertEquals('1.1', $device->getCapability('controlcap_advertised_browser_version'));

        $this->assertFalse($device->getVirtualCapability('is_windows_phone'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_windows_phone'));

        $this->assertFalse($device->getVirtualCapability('is_ios'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_ios'));

        $this->assertTrue($device->getVirtualCapability('is_touchscreen'));
        $this->assertEquals('force_true', $device->getCapability('controlcap_is_touchscreen'));

        $this->assertFalse($device->getVirtualCapability('is_wml_preferred'));
        $this->assertEquals('force_false', $device->getCapability('controlcap_is_wml_preferred'));

        $this->assertEquals('Slab_mask', $device->getVirtualCapability('form_factor'));
        $this->assertEquals('Slab_mask', $device->getCapability('controlcap_form_factor'));

        $this->assertEquals('1.2', $device->getVirtualCapability('advertised_device_os_version'));
        $this->assertEquals('1.2', $device->getCapability('controlcap_advertised_device_os_version'));
    }
}
