<?php

namespace ETelford\Rachio\Tests\Unit;

use ETelford\Rachio\Rachio;

class RachioTest extends TestCase
{
    /** @test */
    public function it_dynamically_loads_a_class_for_an_available_endpoint()
    {
        $rachio = new Rachio('0123456789');

        $this->assertInstanceOf('\ETelford\Rachio\RachioAccount', $rachio->account());
    }

    /**
     * @test
     * @expectedException Exception
     */
    public function it_throws_an_exception_when_an_invalid_endpoint_is_called()
    {
        $rachio = new Rachio('0123456789');
        $rachio->invisible();
    }
}
