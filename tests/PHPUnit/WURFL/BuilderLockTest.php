<?php
/**
 * test case
 */

/**
 * WURFL_BuilderLockTest test case.
 */
class WURFL_BuilderLockTest extends PHPUnit_Framework_TestCase
{
    private $deviceRepositoryBuilder;
    private $lock_dir;

    public function setUp()
    {
        $persistenceProvider           = new WURFL_Storage_Memory();
        $context                       = new WURFL_Context($persistenceProvider);
        $userAgentHandlerChain         = WURFL_UserAgentHandlerChainFactory::createFrom($context);
        $devicePatcher                 = new WURFL_Xml_DevicePatcher();
        $this->deviceRepositoryBuilder = new WURFL_DeviceRepositoryBuilder($persistenceProvider, $userAgentHandlerChain, $devicePatcher);

        $lock_dir_property = new ReflectionProperty('WURFL_DeviceRepositoryBuilder', 'lockFile');
        $lock_dir_property->setAccessible(true);
        $this->lock_dir = $lock_dir_property->getValue($this->deviceRepositoryBuilder);

        if (file_exists($this->lock_dir)) {
            WURFL_FileUtils::rmdirContents($this->lock_dir);
            @rmdir($this->lock_dir);
        }
    }

    public function tearDown()
    {
        if (file_exists($this->lock_dir)) {
            WURFL_FileUtils::rmdirContents($this->lock_dir);
            @rmdir($this->lock_dir);
        }
        unset($this->deviceRepositoryBuilder);
    }

    public function testShouldAcquireLock()
    {
        $this->assertFileNotExists($this->lock_dir);
        $this->assertTrue($this->invokeAcquireLock());
        $this->assertFileExists($this->lock_dir);
    }

    public function testShouldReleaseLock()
    {
        $this->assertTrue($this->invokeAcquireLock());
        $this->invokeReleaseLock();
        $this->assertFileNotExists($this->lock_dir);
    }

    public function testLockShouldBeAlreadyAcquired()
    {
        $this->assertTrue($this->invokeAcquireLock());
        $this->assertFalse($this->invokeAcquireLock());
    }

    public function testMaxAgeLock()
    {
        $this->assertTrue($this->invokeAcquireLock());
        $this->simulateWrongLockStatus();
        sleep(1);
        $this->setExpectedException('Exception', 'Unable to delete lock file [' . $this->lock_dir . ']. Check and fix permissions or delete it manually');
        $this->invokeAcquireLock();
    }

    private function invokeAcquireLock()
    {
        $reflection_method = new ReflectionMethod('WURFL_DeviceRepositoryBuilder', 'acquireLock');
        $reflection_method->setAccessible(true);
        $max_lock_age_property = new ReflectionProperty('WURFL_DeviceRepositoryBuilder', 'maxLockAge');
        $max_lock_age_property->setAccessible(true);
        $max_lock_age_property->setValue($this->deviceRepositoryBuilder, 0);

        return $reflection_method->invoke($this->deviceRepositoryBuilder);
    }

    private function invokeReleaseLock()
    {
        $reflection_method = new ReflectionMethod('WURFL_DeviceRepositoryBuilder', 'releaseLock');
        $reflection_method->setAccessible(true);

        return $reflection_method->invoke($this->deviceRepositoryBuilder);
    }

    private function simulateWrongLockStatus()
    {
        touch($this->lock_dir . '/file.txt');
    }
}
