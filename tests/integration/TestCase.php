<?php

namespace ETelford\Rachio\Tests\Integration;

use ETelford\Rachio\Rachio;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->rachio = new Rachio(static::authorizeFromConfig());
    }

    protected static function authorizeFromConfig()
    {
        $path = __DIR__ . '/config.php';

        if (! is_file($path)) {
            throw new \Exception(static::getMessage());
            die(1);
        }

        $config = require($path);

        return $config['api_key'];
    }

    private static function getMessage()
    {
        $msg = "In order to run the integration tests, you must create a ";
        $msg .= "config.php file in '/tests/integration' that contains your ";
        $msg .= "Rachio API key.";

        return $msg;
    }

    public static function tearDownAfterClass()
    {
        $rachio = new Rachio(static::authorizeFromConfig());

        $rachio->devices()->stop($rachio->devices()->main()->id);
    }
}
