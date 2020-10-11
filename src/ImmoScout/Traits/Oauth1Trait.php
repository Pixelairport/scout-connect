<?php

namespace Pixelairport\ScoutConnect\ImmoScout\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

trait Oauth1Trait
{
    protected $key;
    protected $secret;
    protected $access_token;
    protected $token_secret;

    protected $client;

    protected $responseType = 'array';

    /**
     * Set vars to connect the client.
     *
     * @param $key
     * @param $secret
     * @param null $access_token
     * @param null $token_secret
     * @return Client
     */
    public function connect($key, $secret, $access_token = null, $token_secret = null)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->access_token = $access_token;
        $this->token_secret = $token_secret;

        return $this->getClient();
    }

    /**
     * Set oauth1 client for ImmoScout requests.
     *
     * @return Client
     */
    public function getClient()
    {
        $stack = HandlerStack::create();

        $stack->push(new Oauth1([
            'consumer_key' => $this->key,
            'consumer_secret' => $this->secret,
            'token' => $this->access_token,
            'token_secret' => $this->token_secret
        ]));

        $this->client = new Client([
            'base_uri' => $this->api_base_uri,
            'handler' => $stack,
            'auth' => 'oauth'
        ]);

        return $this->client;
    }

    /**
     * Set the response type.
     * This could be xml, json or array. If it is set to null,
     * you get the direct output from api. It is recommended to set
     * it to json or array.
     *
     * @param $type
     */
    public function setResponseType($type){
        if(in_array(strtolower($type),['xml', 'json', 'array'])){
            $this->responseType = strtolower($type);
        }

        $this->responseType = null;
    }

    /**
     * @param $method
     * @param $endpoint
     * @param array $parameter
     * @return mixed
     */
    public function request($method, $endpoint, $parameter=[])
    {
        // Do the request
        $res = $this->client->request($method, $this->api_base_uri . $endpoint, $parameter);

        // Default return
        if($this->responseType=='xml'){
            return $this->xmlResponse($res);
        }

        // Return response as json
        if($this->responseType=='json'){
            return $this->jsonResponse($res);
        }

        // Return response as array
        if($this->responseType=='array'){
            return $this->arrayResponse($res);
        }

        return $res;
    }

    /**
     * Transform response to xml (array).
     *
     * @param $res
     * @return array
     */
    protected function xmlResponse($res)
    {
        return [
            'code' => $res->getStatusCode(),
            'data' => simplexml_load_string($res->getBody())
        ];
    }

    /**
     * Transform response to json.
     *
     * @param $res
     * @return json
     */
    protected function jsonResponse($res)
    {
        return json_encode([
            'code' => $res->getStatusCode(),
            'data' => json_encode(simplexml_load_string($res->getBody()))
        ]);
    }

    /**
     * Transform response to an array.
     *
     * @param $res
     * @return array
     */
    protected function arrayResponse($res)
    {
        return [
            'code' => $res->getStatusCode(),
            'data' => json_decode(json_encode(simplexml_load_string($res->getBody())), true)
        ];
    }
}
