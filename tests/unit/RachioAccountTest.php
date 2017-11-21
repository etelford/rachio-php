<?php

namespace ETelford\Rachio\Tests\Unit;

use ETelford\Rachio\RachioAccount;

class RachioAccountTest extends TestCase
{
    /** @test */
    public function it_retrieves_an_account_id()
    {
        $account = $this->mockClient(RachioAccount::class, [$this->accountId()])->getId();

        $this->assertEquals(json_decode($this->accountId())->id, $account);
    }

    /** @test */
    public function it_retrieves_an_account()
    {
        $account = $this->mockClient(
            RachioAccount::class,
            [$this->accountId(), $this->account()]
        )->retrieve();

        $this->assertEquals(json_decode($this->account()), $account);
    }
}
