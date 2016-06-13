<?php

namespace Etelford\Rachio;

use GuzzleHttp\Client;
use stdClass;

/**
 * A simple PHP client for the Rachio irrigation API
 * See http://rachio.readme.io/v1.0/docs/ for complete API documentation
 */
class Rachio
{
	/**
	 * @var string The API key
	 */
	private $apiKey;

	/**
	 * @var GuzzleHttp\Client
	 */
	protected $client;

	/**
	 * @var The device to work with
	 */
	protected $device = null;

	/**
	 * @var string The Rachio API end point
	 */
	protected $baseUri = 'https://api.rach.io/1/public/';

	/**
	 * @param string $apiKey
	 */
	public function __construct($apiKey)
	{
		$this->apiKey = $apiKey;
		$this->client = new Client(['base_uri' => $this->baseUri]);
		$this->authorize();
	}

	/**
	 * Authorize into the Rachio API.
	 * Every request must start by getting person:id associated with the 
	 * account.
	 *
	 * https://api.rach.io/1/public/person/info
	 * 
	 * @return statusCode The HTTP status code
	 */
	public function authorize()
	{
		$request = $this->client->get('person/info', $this->setHeaders());

		return [
			'statusCode' => $request->getStatusCode(),
			'personId' => $this->sanitize($request)->id
		];
	}

	/**
	 * Get a person id from the Rachio API.
	 * More friendly method version of `authorize()`
	 *
	 * https://api.rach.io/1/public/person/info
	 * 
	 * @return string The person id
	 */
	public function personId()
	{
		return $this->authorize()['personId'];
	}

	/**
	 * Get a person object from the Rachio API
	 *
	 * https://api.rach.io/1/public/person/:id
	 * 
	 * @return stdClass A person object
	 */
	public function person()
	{
		$request = $this->client->get('person/' . $this->personId(), $this->setHeaders());

		return $this->sanitize($request);
	}

	/**
	 * Convenience method to easily get the `devices` array from a
	 * person object
	 * 
	 * @return stdClass A person object
	 */
	public function devices()
	{
		return $this->person()->devices;
	}

	/**
	 * Get a Rachio device
	 *
	 * https://api.rach.io/1/public/device/:id
	 *
	 * @param  string $id A Rachio device id
	 * @return stdClass a device object
	 */
	public function device($id = null)
	{
		$this->setDevice($id);

		$request = $this->client->get('device/'. $this->device->id, $this->setHeaders());

		return $this->sanitize($request);
	}

	/**
	 * Get the currently running schedule for a Rachio device.
	 * Returns `null` if no schedules are currently running.
	 *
	 * https://api.rach.io/1/public/device/:id/current_schedule
	 * 
	 * @param  string $id A Rachio device id
	 * @return mixed the running schedule or null
	 */
	public function currentSchedule()
	{
		$request = $this->client->get('device/'. $this->device->id . '/current_schedule', $this->setHeaders());
		$currentSchedules = $this->sanitize($request);

		return count(get_object_vars($currentSchedules)) ? $currentSchedules : null;
	}

	/**
	 * Get the next two weeks of scheduled items for a Rachio device
	 *
	 * https://api.rach.io/1/public/device/:id/scheduleitem
	 * 
	 * @param  string $id A Rachio device id
	 * @return mixed the scheduled items or null
	 */
	public function upcomingSchedule()
	{
		$request = $this->client->get('device/'. $this->device->id . '/scheduleitem', $this->setHeaders());
		$upcomingSchedules = $this->sanitize($request);

		return count($upcomingSchedules) ? $upcomingSchedules : null;	
	}

	/**
	 * Start the system in the specified zones for a specified duration.
	 * This is a convenience method that decides whether or not to just start
	 * a single zone or multiple zones.
	 * 
	 * @param  mixed  $zones    The zones to start
	 * @param  integer $duration How long to run for
	 * @return [type]            [description]
	 */
	public function start($zones, $duration = 600)
	{
		if (is_array($zones)) {
			return $this->startMultipleZones($zones, $duration);
		}

		return $this->startSingleZone($zones, $duration);
	}

	/**
	 * Start the system in the specified zone for a specified duration
	 *
	 * https://api.rach.io/1/public/zone/start
	 * 
	 * @param  int  $id    The zone number to start
	 * @param  integer $duration How long to run for
	 * @return int            The status code
	 */
	protected function startSingleZone($zoneNumber, $duration)
	{
		$body = $this->setBody([
			'id' => $this->getZoneId($zoneNumber),
			'duration' => $duration
		]);

		$request = $this->client->put(
			'zone/start', 
			$this->setHeaders() + $body
		);

		return $request->getStatusCode();
	}

	/**
	 * Start the system in the specified zones for a specified duration
	 *
	 * https://api.rach.io/1/public/zone/start_multiple
	 * 
	 * @param  array  $zones    The zones to start
	 * @param  integer $duration How long to run for
	 * @return int            The status code
	 */
	protected function startMultipleZones(array $zones, $duration)
	{
		$body = $this->setBody($this->createZonePayload($zones, $duration));
	
		$request = $this->client->put(
			'zone/start_multiple', 
			$this->setHeaders() + $body
		);

		return $request->getStatusCode();
	}

	/**
	 * Builds up an array for watering multiple zones
	 * 
	 * @param  array $zones    The zones
	 * @param  int $duration Number of seconds to water for
	 * @return array           The array of zones to water
	 */
	protected function createZonePayload(array $zones, $duration)
	{
		$payload = [
			'zones' => []
		];

		foreach ($zones as $counter => $zoneNumber) {
			$payload['zones'][] = [
				'id' => $this->getZoneId($zoneNumber),
				'duration' => $duration,
				'sortOrder' => $counter
			];
		}

		return $payload;		
	}

	/**
	 * Return the 36 character zone id for a given zone number
	 * 
	 * @param  int $zoneNumber The zone number
	 * @return string             The zone id
	 */
	protected function getZoneId($zoneNumber)
	{
		$zones = (is_null($this->device)) ? 
			$this->devices()[0]->zones : 
			$this->device->zones;

		foreach ($zones as $zone) {
			if ($zone->zoneNumber == $zoneNumber) {
				return $zone->id;
			}
		}
	}

	/**
	 * Explicitly set the device
	 * 
	 * @param  string $id The device Id
	 * @return void
	 */
	public function setDevice($id = null)
	{
		if (is_null($id)) {
			$this->device = $this->devices()[0];
		} else {
			$this->device = $this->device($id);
		}

		return $this;
	}

	/**
	 * Set the device to the first one that exists in the account
	 *
	 * @return  void
	 */
	protected function setDefaultDevice()
	{
		$this->setDevice();

		return $this;
	}

	/**
	 * Set the headers for the request
	 *
	 * @return Array The HTTP headers
	 */
	protected function setHeaders()
	{
		return [
			'headers' => [
				'Authorization' => 'Bearer ' . $this->apiKey, 
				'Content-Type' => 'application/json'
			]
		];
	}

	/**
	 * Set the body for the request
	 *
	 * @return Array The body arguments
	 */
	protected function setBody(array $body)
	{
		return ['body' => json_encode($body)];
	}

	/**
	 * Convert the response to JSON
	 * @param  Rachio Response $data A JSON string response from the Rachio API
	 * 
	 * @return stdClass
	 */
	protected function sanitize($request)
	{
		return json_decode($request->getBody()->getContents());
	}
}