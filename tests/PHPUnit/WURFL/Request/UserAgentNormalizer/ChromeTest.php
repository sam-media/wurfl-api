<?php
/**
 *  test case.
 */
class WURFL_Request_UserAgentNormalizer_ChromeTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->normalizer = new WURFL_Request_UserAgentNormalizer_Specific_Chrome();
    }

    /**
     * @test
     * @dataProvider chromeUserAgentsDataProvider
     *
     */
    public function shoudReturnOnlyFirefoxStringWithTheMajorVersion($userAgent, $expected)
    {
        $found = $this->normalizer->normalize($userAgent);
        $this->assertEquals($found, $expected);
    }
        
    
    public function chromeUserAgentsDataProvider()
    {
        return array(
            array("Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/1838444932.621444948.1409104071.2120334063 Safari/537.36", 'Chrome/1838444932.621444948.1409104071.2120334063 Safari/537.36'),
        );
    }
}
