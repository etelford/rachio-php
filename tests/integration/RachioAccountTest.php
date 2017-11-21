<?php

namespace ETelford\Rachio\Tests\Integration;

use ETelford\Rachio\Rachio;

class RachioAccountTest extends TestCase
{
    /** @test */
    public function it_retrieves_an_account_id()
    {
        $accountId = $this->rachio->account()->getId();

        $this->assertGreaterThan(1, strlen($accountId));
    }

    /** @test */
    public function it_retrieves_a_rachio_account()
    {
        $account = $this->getAccount();

        $this->assertObjectHasAttribute('username', $account);
        $this->assertObjectHasAttribute('devices', $account);
    }

    /**
     * Helper to get an account
     *
     * @return Account
     */
    protected function getAccount()
    {
        return $this->rachio->account()->retrieve();
    }
}
