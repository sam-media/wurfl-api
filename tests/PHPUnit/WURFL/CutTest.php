<?php

require_once __DIR__ . '/../../CentralTest/CentralTestManager.php';

/**
 * Central Unit Tests
 */
class WURFL_CutTest extends PHPUnit_Framework_TestCase
{

    public function testCut()
    {
        $config = $this->makeConfig();

        $wurflManagerFactory = new WURFL_WURFLManagerFactory($config);

        $wurfl = $wurflManagerFactory->create();

        $centralTest = new CentralTestManager($wurfl);
        $centralTest->show_success = false;
        ob_start();
        $centralTest->runBatchTest(CentralTestManager::TYPE_ALL);
        ob_end_clean();

        $this->assertEquals(
          0,
          $centralTest->num_failure,
          sprintf('Central Unit Tests failed: %d', $centralTest->num_failure)
        );
    }


    private function makeConfig() {
        // Create WURFL Configuration
        $config = new WURFL_Configuration_InMemoryConfig();

        // Set location of the WURFL File
        $config->wurflFile(__DIR__ . '/../../../examples/resources/wurfl.zip');

        // Setup WURFL Persistence
        $config->persistence('memory');

        // Setup Caching
        $config->cache('null');

        return $config;
    }
}
