<?php
/**
 * test case
 */

/**
 *  test case.
 */
class WURFL_Configuration_ArrayConfigTest
    extends PHPUnit_Framework_TestCase
{

    private $arrayConfig;

    function setUp()
    {
        $configurationFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-array-config.php";
        $this->arrayConfig = new WURFL_Configuration_ArrayConfig($configurationFile);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     */
    public function testShoudThrowInvalidArgumentExceptionForNullConfigurationFilePath()
    {
        $configurationFile = null;
        $arrayConfig       = new WURFL_Configuration_ArrayConfig($configurationFile);
        self::assertNotNull($arrayConfig);
    }

    public function testShouldCreateAConfigFormArrayFile()
    {
        $resourcesDir = dirname(__FILE__) . "/../../../resources";
        $wurflFile    = realpath($resourcesDir . "/wurfl-regression.xml");
        self::assertEquals($wurflFile, $this->arrayConfig->wurflFile);
        $expectedWurlPatches = array(
            realpath("$resourcesDir/web_browsers_patch.xml"),
            realpath("$resourcesDir/spv_patch.xml")
        );
        self::assertAttributeEquals($expectedWurlPatches, "wurflPatches", $this->arrayConfig);
        self::assertTrue($this->arrayConfig->allowReload);
    }

    public function testShoudCreatePersistenceConfiguration()
    {
        $persistence = $this->arrayConfig->persistence;
        self::assertEquals("memcache", $persistence["provider"]);
        self::assertArrayHasKey("params", $persistence);
    }
}

