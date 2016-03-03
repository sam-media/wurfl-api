<?php
/**
 * test case
 */
require_once 'TestUtils.php';

class WURFL_WURFLManagerTest extends PHPUnit_Framework_TestCase
{
    protected $wurflManager;

    protected $persistenceDir;

    public function setUp()
    {
        $resourcesDir = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . "../../resources");

        $config = new WURFL_Configuration_InMemoryConfig();

        $config->wurflFile($resourcesDir . "/wurfl_base.xml");

        // Setup WURFL Persistence
        $this->persistenceDir = sys_get_temp_dir() . '/api_test';
        WURFL_FileUtils::mkdir($this->persistenceDir);
        $config->persistence('file', array('dir' => $this->persistenceDir));

        // Setup Caching
        $config->cache('null');

        $wurflManagerFactory = new WURFL_WURFLManagerFactory($config);
        $this->wurflManager = $wurflManagerFactory->create();
    }

    public function tearDown()
    {
        WURFL_FileUtils::rmdir($this->persistenceDir);
    }

    public function testShouldReturnGenericForEmptyUserAgent()
    {
        $deviceFound = $this->wurflManager->getDeviceForUserAgent('');
        $this->assertEquals('generic', $deviceFound->id);
    }

    public function testShouldReturnGenericForNullUserAgent()
    {
        $deviceFound = $this->wurflManager->getDeviceForUserAgent(null);
        $this->assertEquals('generic', $deviceFound->id);
    }

    public function testShouldReturnWurflVersionInfo()
    {
        $wurflInfo = $this->wurflManager->getWURFLInfo();
        $this->assertEquals("www.wurflpro.com - 2010-06-03 11:55:51", $wurflInfo->version);
        $this->assertEquals("Thu Jun 03 12:01:14 -0500 2010", $wurflInfo->lastUpdated);
    }

    public function testGetListOfGroups()
    {
        $actualGroups = array("product_info", "wml_ui", "chtml_ui", "xhtml_ui", "markup", "display");
        $listOfGroups = $this->wurflManager->getListOfGroups();
        foreach ($actualGroups as $groupId) {
            $this->assertContains($groupId, $listOfGroups);
        }
    }

    /**
     *
     * @dataProvider groupIdCapabilitiesNameProvider
     */
    public function testGetCapabilitiesNameForGroup($groupId, $capabilitiesName)
    {
        $capabilities = $this->wurflManager->getCapabilitiesNameForGroup($groupId);
        $this->assertEquals($capabilitiesName, $capabilities);
    }


    public function groupIdCapabilitiesNameProvider()
    {
        return array(array("chtml_ui", array("chtml_display_accesskey", "emoji", "chtml_can_display_images_and_text_on_same_line", "chtml_displays_image_in_center", "imode_region", "chtml_make_phone_call_string", "chtml_table_support")));
    }
}
