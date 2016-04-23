<?php
/**
 * test case
 */

/**
 * WURFL_BuilderLockTest test case.
 */
class WURFL_ConcurrentAPIsTest extends PHPUnit_Framework_TestCase
{
    const WURFL_DB_1 = '/../../resources/wurfl_base.xml';
    const WURFL_DB_2 = '/../../resources/wurfl_base_concurrent.xml';

    public function setUp()
    {
        if (!extension_loaded('uopz') && !extension_loaded('runkit')) {
            $this->markTestSkipped('Install the uopz or the runkit extension to run this test.');
        }
    }

    public function tearDown()
    {
    }

    public function testShouldAllowConcurrentVersionsCurrentAPI()
    {
        $api1 = $this->makeManager(self::WURFL_DB_1);
        $this->assertEquals($api1->getWURFLInfo()->version, 'www.wurflpro.com - 2010-06-03 11:55:51');
    }

    public function testShouldAllowConcurrentVersionsSimulateDifferentAPI()
    {
        if (extension_loaded('uopz')) {
            \uopz_redefine('WURFL_Constants', 'API_VERSION', 'wurfl-version-fake');
            \uopz_redefine('WURFL_Constants', 'API_NAMESPACE', 'wurfl-namespace-fake');
        }

        if (extension_loaded('runkit')) {
            \runkit_constant_redefine('WURFL_Constants::API_VERSION', 'wurfl-version-fake');
            \runkit_constant_redefine('WURFL_Constants::API_NAMESPACE', 'wurfl-namespace-fake');
        }

        $api2 = $this->makeManager(self::WURFL_DB_2);
        $this->assertEquals($api2->getWURFLInfo()->version, 'wurfl sample concurrent test version');
    }

    private function makeManager($wurfl_file)
    {
        // Create WURFL Configuration
        $wurflConfig = new WURFL_Configuration_InMemoryConfig();

        // Set location of the WURFL File
        $wurflConfig->wurflFile(realpath(__DIR__ . $wurfl_file));

        // Setup WURFL Persistence
        $wurflConfig->persistence('file', array('dir' => WURFL_FileUtils::getTempDir() . '/test_concurrent'));

        // Setup Caching
        $wurflConfig->cache('null');

        // Create a WURFL Manager Factory from the WURFL Configuration
        $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

        // Create a WURFL Manager
        return $wurflManagerFactory->create();
    }
}
