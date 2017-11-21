<?php

namespace ETelford\Rachio\Tests\Unit;

use ETelford\Rachio\RachioDevice;

class RachioDeviceTest extends TestCase
{
    /** @test */
    public function it_retrieves_all_the_devices_for_an_account()
    {
        $devices = $this->mockClient(
            RachioDevice::class,
            [$this->accountId(), $this->devices()]
        )->all();

        $this->assertEquals(json_decode($this->devices())->devices, $devices);
    }

    /** @test */
    public function it_retrieves_a_device_by_its_id()
    {
        $device = $this->mockClient(
            RachioDevice::class,
            [$this->device()]
        )->find('123456789');

        $this->assertEquals(json_decode($this->device()), $device);
    }

    /** @test */
    public function it_retrieves_the_first_device_for_an_account()
    {
        $device = $this->mockClient(
            RachioDevice::class,
            [$this->accountId(), $this->devices(), $this->device()]
        )->first();

        $this->assertEquals(json_decode($this->device()), $device);
    }

    /** @test */
    public function it_retrieves_the_main_device_for_an_account()
    {
        $device = $this->mockClient(
            RachioDevice::class,
            [$this->accountId(), $this->devices(), $this->device()]
        )->main();

        $this->assertEquals(json_decode($this->device()), $device);
    }

    /** @test */
    public function it_retrieves_a_devices_status()
    {
        $status = $this->mockClient(
            RachioDevice::class,
            [$this->device()]
        )->status('0123456789');

        $this->assertEquals(json_decode($this->device())->status, $status);
    }

    /** @test */
    public function it_checks_if_a_device_is_online()
    {
        $online = $this->mockClient(
            RachioDevice::class,
            [$this->device()]
        )->online('0123456789');

        $this->assertTrue($online);
    }

    /** @test */
    public function it_checks_if_a_device_is_offline()
    {
        $online = $this->mockClient(
            RachioDevice::class,
            [$this->device()]
        )->offline('0123456789');

        $this->assertFalse($online);
    }
}
