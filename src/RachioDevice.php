<?php

namespace ETelford\Rachio;

class RachioDevice extends RachioAccount
{
    /**
     * @var The default device to work with
     */
    protected $device = null;

    /**
     * Get all `devices` for the Rachio account
     *
     * @return array
     */
    public function all()
    {
        return $this->retrieve()->devices;
    }

    /**
     * Get a Rachio device from an ID.
     *
     * If no ID is passed, then this will return the first ID
     *
     * https://api.rach.io/1/public/device/:id
     *
     * @param  string $id   A Rachio device id
     * @return \StdClass     A device object
     */
    public function find($id)
    {
        $request = $this->client->get('device/'. $id, $this->setHeaders());

        return $this->sanitize($request);
    }

    /**
     * Get the first device in the Rachio account.
     *
     * https://api.rach.io/1/public/device/:id
     *
     * @param  string $id   A Rachio device id
     * @return \StdClass    A device object
     */
    public function first()
    {
        $this->setDefaultDevice();

        return $this->find($this->device->id);
    }

    /**
     * Get the "main" device in the Rachio account.
     *
     * An alias for `first()`
     *
     * Since most people will only have 1 device, this is a convenient
     * way to get the only device associated with an account.
     *
     * https://api.rach.io/1/public/device/:id
     *
     * @param  string $id   A Rachio device id
     * @return \StdClass    A device object
     */
    public function main()
    {
        return $this->first();
    }

    /**
     * Get the status for the specified device
     *
     * @param  string $id A Rachio device id
     * @return string
     */
    public function status($id)
    {
        return $this->find($id)->status;
    }

    /**
     * Check if the specified device is online
     *
     * @param  string $id   A Rachio device id
     * @return bool
     */
    public function online($id)
    {
        return $this->find($id)->status === "ONLINE";
    }

    /**
     * Check if the specified device is offline
     *
     * @param  string $id   A Rachio device id
     * @return bool
     */
    public function offline($id)
    {
        return ! $this->online($id);
    }

    /**
     * Get the currently running schedule for a Rachio device.
     *
     * Returns `null` if no schedules are currently running.
     *
     * https://api.rach.io/1/public/device/:id/current_schedule
     *
     * @param  string $id               A Rachio device id
     * @return mixed (\StdClass|null)   The running schedule
     */
    public function currentSchedule($id)
    {
        $request = $this->client->get(
            sprintf('device/%s/current_schedule', $id),
            $this->setHeaders()
        );
        $schedules = $this->sanitize($request);

        return count(get_object_vars($schedules)) ? $schedules : null;
    }

    /**
     * Get the next two weeks of scheduled items for a Rachio device
     *
     * Returns `null` if no schedules are found.
     *
     * https://api.rach.io/1/public/device/:id/scheduleitem
     *
     * @param  string $id           A Rachio device id
     * @return mixed (array|null)   The scheduled items or null
     */
    public function next2Weeks($id)
    {
        $request = $this->client->get(
            sprintf('device/%s/scheduleitem', $id),
            $this->setHeaders()
        );
        $schedules = $this->sanitize($request);

        return count($schedules) ? $schedules : null;
    }

    /**
     * Stop a device that's running.
     *
     * @param  string $id   The Rachio device id
     * @return int
     */
    public function stop($id)
    {
        $request = $this->client->put(
            'device/stop_water',
            $this->setHeaders() + $this->setBody(['id' => $id])
        );

        return $request->getStatusCode();
    }

    /**
     * Start the system in the specified zone for a specified duration
     *
     * https://api.rach.io/1/public/zone/start
     *
     * @param  int     $id         The zone id to start
     * @param  int     $duration   How long to run for (seconds)
     * @return int                 The status code
     */
    protected function startZone($id, $duration)
    {
        $body = $this->setBody([
            'id' => $id,
            'duration' => $duration
        ]);

        $request = $this->client->put(
            'zone/start',
            $this->setHeaders() + $body
        );

        return $request->getStatusCode();
    }

    /**
     * Start the system in the specified zones for a specified duration.
     *
     * @param  array   $payload      An array of zone ids and durations
     * @return int
     */
    public function start($arguments)
    {
        if (count($arguments) > 1 && is_array($arguments[0])) {
            return $this->startMultipleZones($arguments);
        }

        return $this->startZone($arguments[0]['id'], $arguments[0]['duration']);
    }

    /**
     * Start the system in the specified zones for a specified duration
     *
     * https://api.rach.io/1/public/zone/start_multiple
     *
     * @param  array  $arguments    The zones to start with durations
     * @return int                  The status code
     */
    protected function startMultipleZones($arguments)
    {
        $body = $this->setBody($this->createZonePayload($arguments));

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
    protected function createZonePayload($arguments)
    {
        $payload = ['zones' => []];

        foreach ($arguments as $index => $zone) {
            $payload['zones'][] = [
                'id' => $zone['id'],
                'duration' => $zone['duration'],
                'sortOrder' => $index
            ];
        }

        return $payload;
    }

    /**
     * Set the device to the first one that exists in the account
     *
     * @return $this
     */
    protected function setDefaultDevice()
    {
        $this->device =  $this->all()[0];

        return $this;
    }
}
