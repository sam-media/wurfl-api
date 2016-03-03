<?php

/**
 * test case.
 */
class WURFL_Request_UserAgentNormalizer_AndroidTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var WURFL_Request_UserAgentNormalizer
     */
    protected $normalizer;

    public function setUp()
    {
        $this->normalizer = WURFL_UserAgentHandlerChainFactory::createGenericNormalizers();
    }

    /**
     * @test
     * @dataProvider normalizerDataProvider
     *
     */
    public function trimsToTwoDigitTheOsVersion($userAgent, $expected)
    {
        $specific = new WURFL_Request_UserAgentNormalizer_Specific_Android();
        $found = $specific->normalize($this->normalizer->normalize($userAgent));
        $this->assertEquals($expected, $found);
    }

    public function normalizerDataProvider()
    {
        return array(
          array("FOO", "FOO"),
          array(
            "Mozilla/5.0 (Linux; U; Android 1.0.15; fr-fr; A70HB Build/CUPCAKE) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2",
            "1.0 A70HB---Mozilla/5.0 (Linux; U; Android 1.0; xx-xx; A70HB Build/CUPCAKE) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2"
          ),
          array(
            "Mozilla/5.0 (Linux; U; Android 2.1-update1; en-us; Hero Build/ERE27) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2",
            "2.1 Hero---Mozilla/5.0 (Linux; U; Android 2.1; xx-xx; Hero Build/ERE27) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2"
          ),
          array(
            "Mozilla/5.0 (Linux; U; Android 2.2.1; en-us; myTouchHD Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1",
            "2.2 myTouchHD---Mozilla/5.0 (Linux; U; Android 2.2; xx-xx; myTouchHD Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1"
          ),
          array(
            "Mozilla/5.0 (Linux; U; Android 2.1-update1; us-usa; SCH-I100 Build/ECLAIR) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2",
            "2.1 usa; SCH-I100---Mozilla/5.0 (Linux; U; Android 2.1; xx-xxusa; SCH-I100 Build/ECLAIR) AppleWebKit/525.10+ (KHTML, like Gecko) Version/3.0.4 Mobile Safari/523.12.2"
          )
        );
    }
}
