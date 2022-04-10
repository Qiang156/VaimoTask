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
    private $condition = [];
    //private $condition = ['brand'=>'maybelline'];
    //private $condition = ['brand'=>'pure anada'];

    /**
     * maximum records to pick up from the source data
     * @var int 0->unlimited
     */
    private $maximum = 0;


    public function __construct() {
        $this->client = new Client([
            'base_uri' => self::HTTP_API,
            'timeout' => self::HTTP_TIMEOUT
        ]);
    }

    /**
     * @return int
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @param $nums
     * @return bool
     */
    public function setMaxinum($nums)
    {
        if($nums > 0) {
            $this->maximum = $nums;
        }
        return true;
    }

    /**
     * http://makeup-api.herokuapp.com/
     * Add filter conditions
     * @param array $condition
     * @return $this
     */
    public function addFilter(string $filter)
    {
        if( $filter == '') return $this;
        $filterArr = explode(';',$filter);
        array_walk($filterArr, function($item) {
            list($key, $val) = explode('=', $item);
            $this->condition[$key] = $val;
        }, $this);
        return $this;
    }


    /**
     * Read makeup data from remote API
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function read(array $argv)
    {
        if( isset($argv['filter']) ) {
            $this->addFilter($argv['filter']);
        }
        if( isset($argv['numbers']) ) {
            $this->setMaxinum($argv['numbers']);
        }
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
        $pickNums = $this->getMaximum();
        if( count($data) > $pickNums ) {
            $data = array_slice($data,0, $pickNums);
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
