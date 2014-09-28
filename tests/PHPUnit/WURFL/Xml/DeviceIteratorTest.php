<?php

/**
 * test case
 */
class WURFL_Xml_DeviceIteratorTest
    extends PHPUnit_Framework_TestCase
{

    const RESOURCES_DIR = "../../../resources/";
    const WURFL_FILE = "../../../resources/wurfl_base.xml";

    /**
     * @expectedException InvalidArgumentException
     */
    public function testShouldLaunchExceptionForInvalidInputFile()
    {
        $wurflFile = "";
        new WURFL_Xml_DeviceIterator ($wurflFile);
    }

    public function testShouldReadTheSpecificAttribute()
    {
        $wurflFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::RESOURCES_DIR . "wurfl-specific-attribute.xml";

        $deviceIterator = new WURFL_Xml_DeviceIterator ($wurflFile);
        $devices        = $this->toList($deviceIterator);

        self::assertEquals("foo", $devices[0]->id);
        self::assertTrue($devices[0]->specific);

        self::assertFalse($devices[1]->specific);
    }

    private function toList($deviceIterator)
    {
        $deviceList = array();
        foreach ($deviceIterator as $device) {
            $deviceList[] = $device;
        }

        return $deviceList;
    }

    public function testShouldLoadAllCapabilties()
    {
        $wurflFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::WURFL_FILE;

        $deviceIterator = new WURFL_Xml_DeviceIterator ($wurflFile);
        foreach ($deviceIterator as $device) {
            $capsByGroupsId = $device->getGroupIdCapabilitiesMap();
            self::assertTrue(count($capsByGroupsId) > 2);
        }
    }

    private function process($device)
    {
        self::assertNotNull($device);
    }
}

