<?php
/**
 * test case
 */

/**
 *  test case.
 */
class WURFL_Configuration_XmlConfigTest
    extends PHPUnit_Framework_TestCase
{

    public function testShouldCreateAConfiguration()
    {
        $configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-config.xml";
        $config     = new WURFL_Configuration_XmlConfig($configPath);
        self::assertNotNull($config->persistence);

        self::assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl.xml", $config->wurflFile);
        self::assertEquals(array(dirname(__FILE__) . DIRECTORY_SEPARATOR . "browsers.xml"), $config->wurflPatches);

        self::assertEquals(true, $config->allowReload);

        $cacheDir    = dirname(__FILE__) . DIRECTORY_SEPARATOR . "cache";
        $persistence = $config->persistence;
        self::assertEquals("file", $persistence ["provider"]);
        self::assertEquals(array(WURFL_Configuration_Config::DIR => $cacheDir), $persistence ["params"]);

        $cache = $config->cache;
        self::assertEquals("file", $cache ["provider"]);
        self::assertEquals(
            array(WURFL_Configuration_Config::DIR => $cacheDir, WURFL_Configuration_Config::EXPIRATION => 36000),
            $cache ["params"]
        );
    }

    public function testShouldCreateConfigurationWithAPCPersistence()
    {
        $configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-config-apc-persistence.xml";
        $config     = new WURFL_Configuration_XmlConfig($configPath);
        self::assertNotNull($config->persistence);

        self::assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl.xml", $config->wurflFile);
        self::assertEquals(array(dirname(__FILE__) . DIRECTORY_SEPARATOR . "browsers.xml"), $config->wurflPatches);

        self::assertEquals(true, $config->allowReload);

        $persistence = $config->persistence;

        self::assertEquals("apc", $persistence ["provider"]);
        self::assertEquals(array("namespace" => "wurflpersist"), $persistence ["params"]);

        $cache = $config->cache;
        self::assertEquals("apc", $cache ["provider"]);
        self::assertEquals(
            array(
                "namespace"  => "wurfl",
                "expiration" => 86400
            ),
            $cache ["params"]
        );
    }

    public function testShouldAcceptEmptyOptionalElements()
    {
        $configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl-config-no-optional.xml";
        $config     = new WURFL_Configuration_XmlConfig($configPath);

        self::assertEquals(dirname(__FILE__) . DIRECTORY_SEPARATOR . "wurfl.xml", $config->wurflFile);
        self::assertEquals(array(), $config->wurflPatches);
        self::assertEquals(false, $config->allowReload);

        $persistence = $config->persistence;
        self::assertEquals("apc", $persistence ["provider"]);
        self::assertEquals(array("namespace" => "wurflpersist"), $persistence ["params"]);

        $cache = $config->cache;
        self::assertTrue(empty($cache));
    }
}

