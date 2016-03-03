<?php
/**
 * test case.
 */
class WURFL_Request_UserAgentNormalizer_FirefoxTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->normalizer = new WURFL_Request_UserAgentNormalizer_Specific_Firefox();
    }
    
    /**
     * @test
     * @dataProvider firefoxUserAgentsDataProvider
     *
     */
    public function shoudReturnOnlyFirefoxStringWithTheMajorVersion($userAgent, $expected)
    {
        $found = $this->normalizer->normalize($userAgent);
        $this->assertEquals($found, $expected);
    }
    
    public function firefoxUserAgentsDataProvider()
    {
        return array(
            array("Mozilla/5.0 (X11; U; Linux armv6l; en-US; rv:1.9a6pre) Gecko/20070810 Firefox/3.0a1", "Firefox/3.0a1" ),
            array("Firefox/3.x", "Firefox/3.x" ),
            array("Mozilla", "Mozilla" ),
            array("Firefox", "Firefox" )
        );
    }
}
