<?php

class WURFL_ApiNamespaceTest extends PHPUnit_Framework_TestCase
{
    public function testApiNamespace()
    {
        $this->assertEquals(
            WURFL_Constants::API_NAMESPACE,
            'wurfl_' . str_replace('.', '', WURFL_Constants::API_VERSION)
        );
    }
}
