<?php

class Tolerance extends PHPUnit_Framework_TestCase
{
    public function testFirstSlash()
    {
        $this->assertEquals(6, WURFL_Handlers_Utils::firstSlash('Value/12'));
        $this->assertNull(WURFL_Handlers_Utils::firstSlash('Value'));
    }

    public function testSecondSlash()
    {
        $this->assertEquals(9, WURFL_Handlers_Utils::secondSlash('Value/12/13'));
        $this->assertNull(WURFL_Handlers_Utils::secondSlash('Value/12'));
        $this->assertNull(WURFL_Handlers_Utils::secondSlash('Value'));
    }

    public function testFirstSpace()
    {
        $this->assertEquals(6, WURFL_Handlers_Utils::firstSpace('Value 12'));
        $this->assertNull(WURFL_Handlers_Utils::firstSpace('Value'));
    }

    public function testOpenParen()
    {
        $this->assertEquals(6, WURFL_Handlers_Utils::firstOpenParen('Value(12)'));
        $this->assertNull(WURFL_Handlers_Utils::firstOpenParen('Value'));
    }

    public function testCloseParen()
    {
        $this->assertEquals(9, WURFL_Handlers_Utils::firstCloseParen('Value(12)'));
        $this->assertNull(WURFL_Handlers_Utils::firstCloseParen('Value'));
    }
}
