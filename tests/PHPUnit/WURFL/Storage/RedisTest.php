<?php
/**
 * test case
 */

/**
 * test case.
 */
class WURFL_Storage_RedisTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('redis') && !class_exists('\Predis\Client')) {
            $this->markTestSkipped('Predis library and Redis extension not present');
        }
    }

    public function testValidPhpRedisInstance()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not present');
        }
        $params          = array();
        $mockRedis       = $this->getMock('\Redis');
        $params['redis'] = $mockRedis;
        $redisStorage    = new WURFL_Storage_Redis($params);
        $this->assertInstanceOf('WURFL_Storage_Redis', $redisStorage);
    }

    /**
     * @requires class_exists('\Predis\Client')
     */
    public function testValidPredisInstance()
    {
        if (!class_exists('\Predis\Client')) {
            $this->markTestSkipped('Predis library not present');
        }
        $mockRedis       = $this->getMock('\Predis\Client');
        $params['redis'] = $mockRedis;
        $redisStorage    = new WURFL_Storage_Redis($params);
        $this->assertInstanceOf('WURFL_Storage_Redis', $redisStorage);
    }

    /**
     * @expectedException WURFL_Storage_Exception
     * @expectedExceptionMessage Connection object is not a Redis or a Predis\Client instance
     */
    public function testInvalidRedisInstance()
    {
        $params          = array();
        $params['redis'] = new stdClass();
        $redisStorage    = new WURFL_Storage_Redis($params);
    }

    public function testParametersAreOverridden()
    {
        $params              = array();
        $params['redis']     = $this->getMockRedisObject();
        $params['host']      = '129.0.0.1';
        $params['port']      = '7654';
        $params['database']  = 2;
        $params['hash_name'] = 'WURFL_DATA_TEST';
        $params['client']    = 'predis';

        $redisStorage = new WURFL_Storage_Redis($params);
        $this->assertInstanceOf('WURFL_Storage_Redis', $redisStorage);
    }

    /**
     * @expectedException WURFL_Storage_Exception
     */
    public function testWrongClient()
    {
        $params              = array();
        $params['host']      = '127.0.0.1';
        $params['port']      = '6379';
        $params['database']  = 2;
        $params['hash_name'] = 'WURFL_DATA_TEST';
        $params['client']    = 'FAIL';

        $redisStorage = new WURFL_Storage_Redis($params);
    }

    public function testParametersAreLoaded()
    {
        if (class_exists('\Predis\Client')) {
            $params              = array();
            $params['host']      = '127.0.0.1';
            $params['port']      = '6379';
            $params['database']  = 2;
            $params['hash_name'] = 'WURFL_DATA_TEST';
            $params['client']    = 'predis';

            try {
                $redisStorage = new WURFL_Storage_Redis($params);
                $this->assertInstanceOf('WURFL_Storage_Redis', $redisStorage);
            } catch (\Predis\Connection\ConnectionException $e) {
                $this->markTestIncomplete(
                    'Could not establish connection to Redis using Predis - This test only works' .
                    'with the standard address of 127.0.0.1:6379 for the Redis server'
                );
            }
        }
        if (class_exists('\Redis')) {
            $params              = array();
            $params['host']      = '127.0.0.1';
            $params['port']      = '6379';
            $params['database']  = 2;
            $params['hash_name'] = 'WURFL_DATA_TEST';
            $params['client']    = 'phpredis';

            try {
                $redisStorage = new WURFL_Storage_Redis($params);
                $this->assertInstanceOf('WURFL_Storage_Redis', $redisStorage);
            } catch (\RedisException $e) {
                $this->markTestIncomplete(
                    'Could not establish connection to Redis using phpredis. This test only works' .
                    'with the standard address of 127.0.0.1:6379 for the Redis server'
                );
            }
        }
    }

    public function testSaveAndLoadObject()
    {
        $value        = new stdClass();
        $value->value = 1;
        $mockRedis    = $this->getMockRedisObject();
        $mockRedis
            ->expects($this->once())
            ->method('hset')
            ->with(
                $this->equalTo('FAKE'),
                $this->equalTo('TEST'),
                $this->equalTo(serialize($value))
            )
            ->willReturn(true);

        $mockRedis
            ->expects($this->once())
            ->method('hget')
            ->with(
                $this->equalTo('FAKE'),
                $this->equalTo('TEST')
            )
            ->willReturn(serialize($value));

        $params              = array();
        $params['redis']     = $mockRedis;
        $params['hash_name'] = 'FAKE';

        $redisStorage = new WURFL_Storage_Redis($params);
        $this->assertTrue($redisStorage->save('TEST', $value, null), 'Save failed');
        $this->assertEquals($value, $redisStorage->load('TEST', $value), 'Save failed');
    }

    public function testSaveAndLoadValue()
    {
        $value     = 1;
        $object    = new StorageObject($value, 0);
        $mockRedis = $this->getMockRedisObject();
        $mockRedis
            ->expects($this->once())
            ->method('hset')
            ->with(
                $this->equalTo('FAKE'),
                $this->equalTo('TEST_VALUE_STORAGE_OBJECT'),
                serialize($object)
            )
            ->willReturn(true);

        $mockRedis
            ->expects($this->once())
            ->method('hget')
            ->with(
                $this->equalTo('FAKE'),
                $this->equalTo('TEST_VALUE_STORAGE_OBJECT')
            )
            ->willReturn(serialize($object));

        $params              = array();
        $params['redis']     = $mockRedis;
        $params['hash_name'] = 'FAKE';

        $redisStorage = new WURFL_Storage_Redis($params);
        $this->assertTrue(
            $redisStorage->save('TEST_VALUE_STORAGE_OBJECT', $value, null),
            'Save failed with StorageObject'
        );
        $this->assertEquals(
            $value,
            $redisStorage->load('TEST_VALUE_STORAGE_OBJECT', $value),
            'Load failed with StorageObject'
        );
    }

    public function testClear()
    {
        $mockRedis = $this->getMockRedisObject();
        $mockRedis
            ->expects($this->once())
            ->method('del')
            ->with(
                $this->equalTo('FAKE')
            )
            ->willReturn(true);

        $params              = array();
        $params['redis']     = $mockRedis;
        $params['hash_name'] = 'FAKE';

        $redisStorage = new WURFL_Storage_Redis($params);
        $this->assertTrue($redisStorage->clear(), 'Clear failed');
    }

    /**
     * Returns a Predis\Client if predis is present, or Redis object if predis is absent
     * and redis extension is present
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockRedisObject()
    {
        if (class_exists('\Predis\Client')) {
            $mockRedis = $this->getMockBuilder('\Predis\Client')
                ->setMethods(array('hset', 'hget', 'del'))
                ->getMock();
        } elseif (extension_loaded('redis')) {
            $mockRedis = $this->getMockBuilder('\Redis')
                ->setMethods(array('hset', 'hget', 'del'))
                ->getMock();
        }

        return $mockRedis;
    }
}
