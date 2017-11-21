<?php

namespace ETelford\Rachio\Tests\Unit;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client as GuzzleClient;
use ETelford\Rachio\Rachio;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Exception\RequestException;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function mockClient($endpoint, array $streams)
    {
        $mock = new MockHandler();

        foreach ($streams as $stream) {
            $mock->append(
                new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    Psr7\stream_for($stream)
                )
            );
        }

        $handler = HandlerStack::create($mock);
        $mockClient = new GuzzleClient(['handler' => $handler]);
        $class = new $endpoint(new Rachio('0123456789'));
        $reflection = new \ReflectionClass($class);
        $reflection_property = $reflection->getProperty('client');
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($class, $mockClient);;

        return $class;
    }

    /**
     * Account ID stub
     *
     * @return string
     */
    protected function accountId()
    {
        return '{"id": "9876543210"}';
    }

    /**
     * Account content stub
     *
     * @return string
     */
    protected function account()
    {
        return '{
            "id": "9876543210",
            "username": "jdoe",
            "fullName": "John Doe",
            "email": "me@example.com",
            "roles": [],
            "managedDevices": [],
            "displayUnit": "US"
        }';
    }

    /**
     * Devices stub
     *
     * @return string
     */
    protected function devices()
    {
        return '{
            "devices": [{
                    "id": "0123456789"
                }, {
                    "id": "9876543210"
            }]
        }';
    }

    /**
     * Devices stub
     *
     * @return string
     */
    protected function singleDevice()
    {
        return '{
            "id": "0123456789"
        }';
    }

    /**
     * Account content stub
     *
     * @return string
     */
    protected function device()
    {
        return '{
            "id": "0123456789",
            "status": "ONLINE",
            "name": "foo"
        }';
    }
}
