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
     * ignore tags from the original data
     * @var string[]
     */
    private $delete = [
        'product_link','website_link','product_api_url','api_featured_image'
    ];

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
        if( isset($argv['numbers']) && $argv['numbers'] > 0 ) {
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
        if( $pickNums > 0 && count($data) > $pickNums ) {
            $data = array_slice($data,0, $pickNums);
        }
        return $this->addExtraItem($data, $argv);
    }

    /**
     * @param array $data
     * @return array
     */
    public function addExtraItem(array $data, array $option)
    {
        foreach($data as $key => $item) {
            foreach($item as $k => $v) {
                if( !is_array($v) ) $data[$key][$k] = htmlspecialchars(trim($v));
            }
            $data[$key]['short_description'] = $item['description'];
            $data[$key]['status'] = 'enabled';
            $data[$key]['visibility'] = 'both';
            $data[$key]['reset_website_ids'] = 'true';

//            $colors = $this->handleColors($item);
//            $data[$key]['color_name'] = $colors['color_name'];
//            $data[$key]['color_hex'] = $colors['color_hex'];
//            unset($data[$key]['product_colors']);

//            if( !empty($data[$key][$option['attributes']]) && $option['type'] == 'configurable') {
//                $data[$key]['type'] = $option['type'];
//                $data[$key]['configurable_attributes'] = $option['attributes'];
//            }

            foreach($this->delete as $keyword) {
                if( isset($item[$keyword]) ) {
                    unset($data[$key][$keyword]);
                }
            }

        }
        return $data;
    }

    /**
     * @param $product
     * @return void
     */
    private function handleColors($product)
    {
        $colors = [
            'color_name'=>[],
            'color_hex'=>[]
        ];
        if( isset($product['product_colors']) && !empty($product['product_colors']) ) {
            foreach( $product['product_colors'] as $item ) {
                $colors['color_name'][] = htmlspecialchars($item['colour_name']);
                $colors['color_hex'][] = trim(strtoupper($item['hex_value']),'#');
            }
        }
        return $colors;
    }

}
