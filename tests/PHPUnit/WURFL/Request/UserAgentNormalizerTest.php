<?php

/**
 * test case
 */
class WURFL_Request_UserAgentNormalizerTest
    extends PHPUnit_Framework_TestCase
{

    function testShouldAddANormalizer()
    {
        $userAgentNormalizer = new WURFL_Request_UserAgentNormalizer();
        $currentNormalizer   = $userAgentNormalizer->addUserAgentNormalizer(
            new WURFL_Request_UserAgentNormalizer_Specific_Chrome()
        );

        self::assertEquals(0, $userAgentNormalizer->count());
        self::assertEquals(1, $currentNormalizer->count());
    }

    function testShouldAddToAlreadyPresentNormalizers()
    {
        $userAgentNormalizer = new WURFL_Request_UserAgentNormalizer(
            array(new WURFL_Request_UserAgentNormalizer_Generic_BabelFish())
        );
        $userAgentNormalizer = $userAgentNormalizer->addUserAgentNormalizer(
            new WURFL_Request_UserAgentNormalizer_Specific_Chrome()
        );

        self::assertEquals(2, $userAgentNormalizer->count());
    }
}