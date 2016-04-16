<?php

require_once __DIR__ . '/../../CentralTest/CentralTestManager.php';

/**
 * Central Unit Tests
 */
class WURFL_CutTest extends PHPUnit_Framework_TestCase
{
    public function testCut()
    {
        $test_list = CentralTestManager::loadBatchTest(CentralTestManager::TYPE_ALL);

        $config = $this->makeConfig();

        $wurflManagerFactory = new WURFL_WURFLManagerFactory($config);
        $wurfl               = $wurflManagerFactory->create();

        $centralTest               = new CentralTestManager($wurfl, $test_list);
        $centralTest->show_success = false;
        ob_start();
        $centralTest->run();
        ob_end_clean();

        $this->assertEquals(
            0,
            $centralTest->num_failure,
            sprintf('Central Unit Tests failed: %d', $centralTest->num_failure)
        );
    }

    public function testCutWithFilter()
    {
        $test_list     = CentralTestManager::loadBatchTest(CentralTestManager::TYPE_ALL);
        $required_caps = CentralTestManager::getRequiredCapsFromTestList($test_list);

        $config = $this->makeConfig($required_caps);

        $wurflManagerFactory = new WURFL_WURFLManagerFactory($config);
        $wurfl               = $wurflManagerFactory->create();

        $centralTest               = new CentralTestManager($wurfl, $test_list);
        $centralTest->show_success = false;
        ob_start();
        $centralTest->run();
        ob_end_clean();

        $this->assertEquals(
            0,
            $centralTest->num_failure,
            sprintf('Central Unit Tests failed: %d', $centralTest->num_failure)
        );
    }

    private function makeConfig($required_caps = array())
    {
        // Create WURFL Configuration
        $config = new WURFL_Configuration_InMemoryConfig();

        // Set location of the WURFL File
        $config->wurflFile(__DIR__ . '/../../../examples/resources/wurfl.zip');

        if (!empty($required_caps)) {
            $config->capabilityFilter($required_caps);
        }

        // Setup WURFL Persistence
        $config->persistence('memory');

        // Setup Caching
        $config->cache('null');

        return $config;
    }
}
