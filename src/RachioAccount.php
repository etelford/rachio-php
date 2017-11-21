<?php

namespace ETelford\Rachio;

use ETelford\Rachio\Client as BaseClient;

class RachioAccount extends BaseClient
{
    /**
     * Get a person object from the Rachio API
     *
     * https://api.rach.io/1/public/person/:id
     *
     * @return stdClass A person object
     */
    public function retrieve()
    {
        $request = $this->client->get(
            'person/' . $this->authorize(), $this->setHeaders()
        );

        return $this->sanitize($request);
    }

    /**
     * Get just the account ID for the Rachio account.
     *
     * https://api.rach.io/1/public/person/info
     *
     * @return string The person id
     */
    public function getId()
    {
        return $this->authorize();
    }
}
