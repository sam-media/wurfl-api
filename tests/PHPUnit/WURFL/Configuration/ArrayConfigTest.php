<?php
/**
 * test case
 */

/**
 *  test case.
 */
class WURFL_Configuration_ArrayConfigTest extends PHPUnit_Framework_TestCase
{
    private $arrayConfig;
    

    
    public function setUp()
    {
        $configurationFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-array-config.php";
        mkdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache');
        $this->arrayConfig = new WURFL_Configuration_ArrayConfig($configurationFile);
    }

    public function tearDown()
    {
        rmdir(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cache');
    }
    
    /**
     * @expectedException InvalidArgumentException
     *
     */
    public function testShoudThrowInvalidArgumentExceptionForNullConfigurationFilePath()
    {
        $configurationFile = null;
        $arrayConfig = new WURFL_Configuration_ArrayConfig($configurationFile);
        $this->assertNotNull($arrayConfig);
    }
    
    public function testShouldCreateAConfigFormArrayFile()
    {
        $resourcesDir = dirname(__FILE__) . "/../../../resources";
        $wurflFile = realpath($resourcesDir . "/wurfl-regression.xml");
        $this->assertEquals($wurflFile, $this->arrayConfig->wurflFile);
        $expectedWurlPatches = array(realpath("$resourcesDir/web_browsers_patch.xml"), realpath("$resourcesDir/spv_patch.xml"));
        $this->assertAttributeEquals($expectedWurlPatches, "wurflPatches", $this->arrayConfig);
        $this->assertTrue($this->arrayConfig->allowReload);
    }
    
    
    public function testShoudCreatePersistenceConfiguration()
    {
        $persistence = $this->arrayConfig->persistence;
        $this->assertEquals("memcache", $persistence["provider"]);
        $this->assertArrayHasKey("params", $persistence);
    }
}
