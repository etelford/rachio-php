<?php
 
use ETelford\Rachio\Rachio;

class RachioTest extends \PHPUnit_Framework_TestCase
{
	protected $rachio;

	protected $baseResponse;

	public function setUp()
	{
		parent::setUp();

		$this->rachio = new Rachio();

		$this->getBaseResponse();
	}

    /** @test */
    public function it_authorizes_into_the_rachio_api()
    {
    	$this->assertEquals(200, $this->baseResponse['statusCode']);
    }

    /** @test */
    public function it_retrieves_a_person_id()
    {
    	$personId = $this->rachio->personId();

    	$this->assertEquals($this->baseResponse['personId'], $personId);
    }

    /** @test */
    public function it_retrieves_a_person()
    {
    	$person = $this->rachio->person();

    	$this->assertEquals($this->baseResponse['personId'], $person->id);
    	$this->assertObjectHasAttribute('username', $person);
    }

    /** @test */
    public function it_retrieves_all_the_devices_for_an_account()
    {
        $devices = $this->devices();

        $this->assertGreaterThanOrEqual(1, count($devices));
        $this->assertObjectHasAttribute('id', $devices[0]);
    }

    /** @test */
    public function it_retrieves_a_specified_device()
    {
        $allDevices = $this->devices();
        $device = $this->rachio->device($allDevices[0]->id);

        $this->assertObjectHasAttribute('id', $device);
        $this->assertEquals($allDevices[0]->id, $device->id);
    }

    /** @test */
    public function it_retrieves_the_next_two_weeks_of_scheduled_items_for_a_device()
    {
        $allDevices = $this->devices();
        $schedules = $this->rachio->upcomingSchedule($allDevices[0]->id);

        $this->assertGreaterThanOrEqual(1, count($schedules));
    }

    /** @test */
    public function it_starts_a_zone_for_a_device()
    {
        $this->assertEquals(204, $this->rachio->start(6, 10));
    }

    /** @test */
    public function it_starts_multiple_zones_for_a_device()
    {
        $this->assertEquals(204, $this->rachio->start([6, 8], 10));
    }

    protected function getBaseResponse()
    {
    	$this->baseResponse = $this->rachio->authorize();
    }

    protected function devices()
    {
        return $this->rachio->devices();
    }
}
