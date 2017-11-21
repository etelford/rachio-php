<?php

namespace ETelford\Rachio;

/**
 * A simple PHP client for the Rachio irrigation API
 *
 * See http://rachio.readme.io/v1.0/docs/ for complete API documentation
 */
class Rachio
{
	/**
	 * @var string The API key
	 */
	protected $apiKey;

    /**
     * The namespace for dynamically created classes
     */
    const NAMESPACE = '\\ETelford\\Rachio';

	/**
	 * @param string $apiKey
	 */
	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;
	}

    /**
     * Dynamically instantiate the requested Rachio class
     *
     * @param  string   $method
     * @param  array    $arguments
     * @return Class
     */
    public function __call($method, $arguments)
    {
        $class = sprintf(
            '%s\\Rachio%s',
            static::NAMESPACE,
            ucwords(rtrim($method, 's'))
        );

        if (! class_exists($class)) {
            throw new \Exception('You have attempted to call an invalid endpoint.');
        }

        return new $class($this);
    }

    /**
     * Getter for the API Key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }
}
