<?php
/**
 * test case
 */

/**
 * test case.
 */
class WURFL_Storage_MemoryTest extends PHPUnit_Framework_TestCase {
	
		
	public function testNeverToExpireItems() {
		$storage = new WURFL_Storage_Memory();	
		$storage->save("foo", "foo");
		sleep(2);
		$this->assertEquals("foo", $storage->load("foo"));			
	}

	public function testShouldClearAllItems() {
		$storage = new WURFL_Storage_Memory(array());
		$storage->save("key1", "item1");		
		$storage->save("key2", "item2");
		$storage->clear();
		
		$this->assertThatNoElementsAreInCache(array("key1", "key2"), $storage);
		
	}

	private function assertThatNoElementsAreInCache($keys = array(), $storage) {
		foreach ($keys as $key) {
			$this->assertNull($storage->load($key));
		}
	}
	
}
