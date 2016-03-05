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
}
