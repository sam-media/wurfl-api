<?php
/**
 * test case
 */

/**
 * WURFL_DeviceRepositoryBuilder test case.
 */
class WURFL_DeviceRepositoryBuilderTest extends PHPUnit_Framework_TestCase
{
    const WURFL_FILE = "../../resources/wurfl_base.xml";
    const WURFL_INVALID_DEVICES = "../../resources/wurfl-test-required-devices.xml";
    const WURFL_INVALID_CAPABILITIES = "../../resources/wurfl-test-required-caps.xml";
    const PATCH_INVALID_FALLBACK = "../../resources/patch-invalid-fallback.xml";
    const PATCH_FILE_ONE = "../../resources/patch1.xml";
    const PATCH_FILE_TWO = "../../resources/patch2.xml";
    
    private $deviceRepositoryBuilder;
    
    public function setUp()
    {
        $persistenceProvider = new WURFL_Storage_Memory();
        $context = new WURFL_Context($persistenceProvider);
        $userAgentHandlerChain = WURFL_UserAgentHandlerChainFactory::createFrom($context);
        $devicePatcher = new WURFL_Xml_DevicePatcher();
        $this->deviceRepositoryBuilder = new WURFL_DeviceRepositoryBuilder($persistenceProvider, $userAgentHandlerChain, $devicePatcher);
    }

    public function tearDown()
    {
        unset($this->deviceRepositoryBuilder);
    }

    public function testMissedRequiredDevices()
    {

        $wurflFile = dirname(__FILE__). DIRECTORY_SEPARATOR . self::WURFL_INVALID_DEVICES;
        $this->setExpectedException('WURFL_Exception_WURFLConsistencyException', 'wurfl.xml load error - you may need to update the wurfl.xml file to a more recent version');
        $this->deviceRepositoryBuilder->build($wurflFile);
    }

    public function testMissedRequiredCapabilities()
    {
        $wurflFile = dirname(__FILE__). DIRECTORY_SEPARATOR . self::WURFL_INVALID_CAPABILITIES;
        $this->setExpectedException('WURFL_Exception_WURFLConsistencyException', 'Missing required WURFL Capabilities: ux_full_desktop, is_tablet, pointing_method, device_os_version, can_assign_phone_number, mobile_browser_version, marketing_name, is_smarttv');
        $this->deviceRepositoryBuilder->build($wurflFile);
    }

    public function testInvalidFallback()
    {
        $wurflFile = dirname(__FILE__). DIRECTORY_SEPARATOR . self::WURFL_FILE;
        $patchFile = dirname(__FILE__). DIRECTORY_SEPARATOR . self::PATCH_INVALID_FALLBACK;

        $this->setExpectedException('WURFL_Exception_WURFLConsistencyException', 'Invalid Fallback invalid-fallback for the device invalid-id');
        $this->deviceRepositoryBuilder->build($wurflFile, array($patchFile));
    }

    public function testShouldBuildARepositoryOfAllDevicesFromTheXmlFile()
    {
        $wurflFile = dirname(__FILE__). DIRECTORY_SEPARATOR . self::WURFL_FILE;


        $deviceRepository = $this->deviceRepositoryBuilder->build($wurflFile);
        $this->assertNotNull($deviceRepository);
        $this->assertEquals("Thu Jun 03 12:01:14 -0500 2010", $deviceRepository->getLastUpdated());
        $genericDevice = $deviceRepository->getDevice("generic");
        $this->assertNotNull($genericDevice, "generic device is null");
    }

    public function testShouldAddNewDevice()
    {
        $wurflFile = dirname(__FILE__). DIRECTORY_SEPARATOR . self::WURFL_FILE;
        $patchFile1 = dirname(__FILE__). DIRECTORY_SEPARATOR . self::PATCH_FILE_ONE;

        $deviceRepository = $this->deviceRepositoryBuilder->build($wurflFile, array($patchFile1));
        $this->assertNotNull($deviceRepository);
        $newDevice1 = $deviceRepository->getDevice("generic_web_browser");
        $this->assertNotNull($newDevice1, "generic web browser device is null");
        $this->assertEquals("770", $newDevice1->getCapability("columns"));
    }

    public function testShouldApplyMoreThanOnePatches()
    {
        $wurflFile = dirname(__FILE__). DIRECTORY_SEPARATOR . self::WURFL_FILE;
        $patchFile1 = dirname(__FILE__). DIRECTORY_SEPARATOR . self::PATCH_FILE_ONE;
        $patchFile2 = dirname(__FILE__). DIRECTORY_SEPARATOR . self::PATCH_FILE_TWO;

        $deviceRepository = $this->deviceRepositoryBuilder->build($wurflFile, array($patchFile1, $patchFile2));
        $this->assertNotNull($deviceRepository);
        $newDevice1 = $deviceRepository->getDevice("generic_web_browser");
        $this->assertNotNull($newDevice1, "generic web browser device is null");
        $this->assertEquals("770", $newDevice1->getCapability("columns"));

        $newDevice2 = $deviceRepository->getDevice("generic_web_browser_new");
        $this->assertNotNull($newDevice2, "generic web browser device is null");
        $this->assertEquals("7", $newDevice2->getCapability("columns"));
    }

    public function testShouldNotRebuildTheRepositoryIfAlreadyBuild()
    {
        $persistenceProvider = new WURFL_Storage_Memory();
        $persistenceProvider->setWURFLLoaded(true);
        $context = new WURFL_Context($persistenceProvider);
        $userAgentHandlerChain = WURFL_UserAgentHandlerChainFactory::createFrom($context);
        $devicePatcher = new WURFL_Xml_DevicePatcher();
        $deviceRepositoryBuilder = new WURFL_DeviceRepositoryBuilder($persistenceProvider, $userAgentHandlerChain, $devicePatcher);
        $this->assertNotNull($deviceRepositoryBuilder);
        $wurflFile = dirname(__FILE__). DIRECTORY_SEPARATOR . self::WURFL_FILE;

        try {
            $deviceRepository = $deviceRepositoryBuilder->build($wurflFile);
            $deviceRepository->getDevice("generic");
        } catch (Exception $ex) {
        }
    }

}
