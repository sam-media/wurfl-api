<?php
/**
 * test case
 */

/**
 *  test case.
 */
class WURFL_Configuration_XmlConfigTest extends PHPUnit_Framework_TestCase {


    public function testShouldCreateAConfiguration() {
        $configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-config.xml";
        $config = new WURFL_Configuration_XmlConfig($configPath);
        $this->assertNotNull($config->persistence);

        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl.xml", $config->wurflFile);
        $this->assertEquals(array(dirname(__FILE__) . DIRECTORY_SEPARATOR . "browsers.xml"), $config->wurflPatches);

        $this->assertEquals(true, $config->allowReload);

        $cacheDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . "cache";
        $persistence = $config->persistence;
        $this->assertEquals("file", $persistence ["provider"]);
        $this->assertEquals(array(WURFL_Configuration_Config::DIR => $cacheDir), $persistence ["params"]);

        $cache = $config->cache;
        $this->assertEquals("file", $cache ["provider"]);
        $this->assertEquals(array(WURFL_Configuration_Config::DIR => $cacheDir, WURFL_Configuration_Config::EXPIRATION => 36000), $cache ["params"]);

    }


    public function testShouldCreateConfigurationWithAPCPersistence() {
        $configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-config-apc-persistence.xml";
        $config = new WURFL_Configuration_XmlConfig($configPath);
        $this->assertNotNull($config->persistence);

        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl.xml", $config->wurflFile);
        $this->assertEquals(array(dirname(__FILE__) . DIRECTORY_SEPARATOR . "browsers.xml"), $config->wurflPatches);

        $this->assertEquals(true, $config->allowReload);

        $persistence = $config->persistence;

        $this->assertEquals("apc", $persistence ["provider"]);
        $this->assertEquals(array("namespace" => "wurflpersist"), $persistence ["params"]);

        $cache = $config->cache;
        $this->assertEquals("apc", $cache ["provider"]);
        $this->assertEquals(array(
            "namespace" => "wurfl",
            "expiration" => 86400), $cache ["params"]);

    }


    public function testShouldAcceptEmptyOptionalElements() {
        $configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-config-no-optional.xml";
        $config = new WURFL_Configuration_XmlConfig($configPath);

        $this->assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl.xml", $config->wurflFile);
        $this->assertEquals(array(), $config->wurflPatches);
        $this->assertEquals(false, $config->allowReload);

        $persistence = $config->persistence;
        $this->assertEquals("apc", $persistence ["provider"]);
        $this->assertEquals(array("namespace" => "wurflpersist"), $persistence ["params"]);

        $cache = $config->cache;
        $this->assertTrue(empty($cache));

    }


}

