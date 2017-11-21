<?php

namespace ETelford\Rachio\Tests\Integration;

use ETelford\Rachio\RachioDevice;

class RachioDeviceTest extends TestCase
{
    /** @test */
    public function it_retrieves_all_the_devices_for_an_account()
    {
        $devices = $this->getDevices();

        $this->assertGreaterThanOrEqual(1, count($devices));
        $this->assertObjectHasAttribute('id', $devices[0]);
    }

    /** @test */
    public function it_retrieves_a_specified_device()
    {
        $allDevices = $this->getDevices();
        $device = $this->rachio->devices()->find($allDevices[0]->id);

        $this->assertObjectHasAttribute('id', $device);
        $this->assertEquals($allDevices[0]->id, $device->id);
    }

    /** @test */
    public function it_retrieves_the_main_device_in_an_account_when_no_device_is_specified()
    {
        $allDevices = $this->getDevices();
        $mainDevice = $this->rachio->devices()->main();

        $this->assertObjectHasAttribute('id', $mainDevice);
        $this->assertEquals($allDevices[0]->id, $mainDevice->id);
    }

    /** @test */
    public function it_retrieves_the_first_device_in_an_account_when_no_device_is_specified()
    {
        $allDevices = $this->getDevices();
        $firstDevice = $this->rachio->devices()->first();

        $this->assertObjectHasAttribute('id', $firstDevice);
        $this->assertEquals($allDevices[0]->id, $firstDevice->id);
    }

    public function it_retrieves_the_next_two_weeks_of_scheduled_items_for_a_device()
    {
        $mainDevice = $this->rachio->devices()->main();
        $schedules = $this->devices()->upcomingSchedule($mainDevice->id);

        $this->assertGreaterThanOrEqual(1, count($schedules));
    }

    public function it_starts_a_zone_for_a_device()
    {
        $this->assertEquals(204, $this->account->start(6, 10));
    }

    public function it_starts_multiple_zones_for_a_device()
    {
        $this->assertEquals(204, $this->account->start([6, 8], 10));
    }

    protected function getDevices()
    {
        return $this->rachio->devices()->all();
    }
}
