<?php

namespace Pixelairport\ScoutConnect\ImmoScout;

use Pixelairport\ScoutConnect\ImmoScout\Traits\Oauth1Trait;

class User
{
    use Oauth1Trait;

    protected $api_base_uri = 'https://rest.immobilienscout24.de/restapi/api';

    /**
     * User constructor.
     * Constructor create a client with immoscout connection.
     *
     * @param $key
     * @param $secret
     * @param null $access_token
     * @param null $token_secret
     */
    public function __construct($key, $secret, $access_token = null, $token_secret = null)
    {
        $this->connect($key, $secret, $access_token, $token_secret);
    }

    /**
     * Get all real estate offers from a user.
     *
     * @param array
     * @return mixed
     */
    public function findOffers($parameter=[])
    {
        return $this->request('GET', '/offer/v1.0/user/me/realestate', $parameter);
    }
}
