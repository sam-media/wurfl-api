<?php
/**
 * test case
 */

/**
 * test case.
 */
class WURFL_Storage_ApcTest extends PHPUnit_Framework_TestCase
{
    public function testApiNamespace()
    {
        $this->checkDeps();
        $reflection_method = new ReflectionMethod('WURFL_Storage_Apc', 'encode');
        $reflection_method->setAccessible(true);
        $namespaced_id = $reflection_method->invoke(new WURFL_Storage_Apc(), 'namespace', 'id');
        $this->assertEquals(WURFL_Constants::API_NAMESPACE . ':namespace:id', $namespaced_id);
    }

    public function testDefaultApcNamespace()
    {
        $this->checkDeps();
        $reflection_method = new ReflectionMethod('WURFL_Storage_Apc', 'apcNameSpace');
        $reflection_method->setAccessible(true);
        $apc_namespace = $reflection_method->invoke(new WURFL_Storage_Apc());
        $this->assertEquals('wurfl', $apc_namespace);
    }

    public function testCustomApcNamespace()
    {
        $this->checkDeps();
        $reflection_method = new ReflectionMethod('WURFL_Storage_Apc', 'apcNameSpace');
        $reflection_method->setAccessible(true);
        $apc_namespace = $reflection_method->invoke(new WURFL_Storage_Apc(array('namespace' => 'custom')));
        $this->assertEquals('custom', $apc_namespace);
    }

    public function testNeverToExpireItems()
    {
        $this->checkDeps();
        $storage = new WURFL_Storage_Apc();
        $storage->save('foo', 'foo');
        sleep(2);
        $this->assertEquals('foo', $storage->load('foo'));
    }

    /*
      *  Need to make two request to test this.
      *  http://pecl.php.net/bugs/bug.php?id=13331
      *
      *
    public function testShouldRemoveTheExpiredItem() {

        $params = array(WURFL_Configuration_Config::EXPIRATION => 1);
        $storage = new WURFL_Storage_Apc($params);
        $storage->save("key", "value");
        sleep(2);
        $this->assertEquals(NULL, $storage->load("key"));
    }
    */

    public function testShouldClearAllItems()
    {
        $this->checkDeps();
        $storage = new WURFL_Storage_Apc(array());
        $storage->save('key1', 'item1');
        $storage->save('key2', 'item2');
        $storage->clear();

        $this->assertThanNoElementsAreInCache(array('key1', 'key2'), $storage);
    }

    private function assertThanNoElementsAreInCache($keys = array(), WURFL_Storage_Apc $storage)
    {
        foreach ($keys as $key) {
            $this->assertNull($storage->load($key));
        }
    }

    private function checkDeps()
    {
        if (!extension_loaded(WURFL_Storage_Apc::EXTENSION_MODULE_NAME) || ini_get('apc.enable_cli') === false) {
            $this->markTestSkipped("PHP extension 'apc' must be loaded and enabled for CLI to run this test (http://www.php.net/manual/en/apc.configuration.php#ini.apc.enable-cli).");
        }
    }
}
