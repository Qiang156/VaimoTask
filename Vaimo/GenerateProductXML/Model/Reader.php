<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Model;

use GuzzleHttp\Client;

/**
 * File reader class
 */
class Reader implements ReaderInterface
{
    /**
     * baseUrl
     */
    const HTTP_API = 'http://makeup-api.herokuapp.com/api/v1/';
    /**
     * Http timeout
     */
    const HTTP_TIMEOUT = 30;
    /**
     * @var Client
     */
    private $client = null;
    /**
     * Conditions
     * @var string[]
     */
    private $condition = ['brand'=>'maybelline'];

    public function __construct() {
        $this->client = new Client([
            'base_uri' => self::HTTP_API,
            'timeout' => self::HTTP_TIMEOUT
        ]);
    }

    /**
     * http://makeup-api.herokuapp.com/
     * Add filter conditions
     * @param array $condition
     * @return $this
     */
    public function addFilter(array $condition)
    {
        foreach ($condition as $key => $value) {
            $this->condition[$key] = $value;
        }
        return $this;
    }


    /**
     * Read makeup data from remote API
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function read()
    {
        $data = [];
        try {
            $query_uri = 'products.json?'.http_build_query($this->condition,'','&');
            $request = $this->client->request('GET', $query_uri);
            if ( $request->getStatusCode() === 200 ) {
                $data = json_decode($request->getBody(),true);
            }
        } catch ( GuzzleException $e ) {
            echo "GuzzleException Error: ".$e->getMessage();
            $this->client = null;
        }
        return $this->addExtraItem($data);
    }

    /**
     * @param array $data
     * @return array
     */
    public function addExtraItem(array $data)
    {
        foreach($data as $key => $item) {
            $data[$key]['short_description'] = $item['description'];
            $data[$key]['status'] = 'enabled';
            $data[$key]['visibility'] = 'both';
            $data[$key]['reset_website_ids'] = 'true';
        }
        return $data;
    }

}
