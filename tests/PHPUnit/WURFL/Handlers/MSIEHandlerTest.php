<?php
/**
 * test case
 */
/**
 * test case.
 */
class WURFL_Handlers_MSIEHandlerTest extends PHPUnit_Framework_TestCase {
	
	private $msieHandler;
	
	function setUp() {
		$context = new WURFL_Context ( null );
		$userAgentNormalizer = new WURFL_Request_UserAgentNormalizer_Specific_MSIE ();
		$this->msieHandler = new WURFL_Handlers_MSIEHandler ($context, $userAgentNormalizer );
	}
	
	function testShoudHandle() {
		$userAgent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)";
		$this->assertTrue ( $this->msieHandler->canHandle ( $userAgent ) );
	}

}

