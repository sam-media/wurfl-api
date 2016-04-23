<?php
/**
 * test case
 */

class WURFL_Xml_DeviceIteratorTest extends PHPUnit_Framework_TestCase
{
    const RESOURCES_DIR = '../../../resources/';
    const WURFL_FILE    = '../../../resources/wurfl_base.xml';

    /**
     * @expectedException InvalidArgumentException
     */
    public function testShouldLaunchExceptionForInvalidInputFile()
    {
        $wurflFile = '';
        new WURFL_Xml_DeviceIterator($wurflFile);
    }

    public function testShouldReadTheSpecificAttribute()
    {
        $wurflFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . self::RESOURCES_DIR . 'wurfl-specific-attribute.xml';

        $deviceIterator = new WURFL_Xml_DeviceIterator($wurflFile);
        $devices        = $this->toList($deviceIterator);

        $this->assertEquals('foo', $devices[0]->id);
        $this->assertTrue($devices[0]->specific, 'device "foo" is not specific, this is wrong');

        $this->assertEquals('bar', $devices[1]->id);
        $this->assertFalse($devices[1]->specific, 'device "bar" is specific, this is wrong');
    }

    private function toList($deviceIterator)
    {
        $deviceList = array();
        foreach ($deviceIterator as $device) {
            $deviceList[] = $device;
        }

        return $deviceList;
    }
}
