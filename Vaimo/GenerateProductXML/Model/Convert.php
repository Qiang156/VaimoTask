<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Model;


/**
 * File reader interface
 */
class Convert implements ConvertInterface
{

    /**
     * ['title'=>'name'] means modify array key-value 'name' => 'title';
     * ['title'=>['name','brand'] means adding title keyword with combinetion of name and brand;
     * @var array
     */
    private $maps = [
        'sku' => 'id',
        'colors' => 'product_colors',
        'category' => 'brand',
        'tags' => 'tag_list',
        'images' => 'image_link'
    ];

    /**
     * ignore tags from the original data
     * @var string[]
     */
    private $delete = [
        'product_link','website_link','product_api_url','api_featured_image'
    ];

    /**
     * @param string $key
     * @param string|array $data
     * @return $this
     */
    public function map(string $key, mixed $data)
    {
        $this->maps[$key] = $data;
        return $this;
    }

    /**
     * @param array $list
     * @return $this
     */
    public function delete(array $list)
    {
        foreach($list as $key) {
            $this->delete[] = $key;
        }
        return $this;
    }

    /**
     * convert array with other information such as key word.
     * @param $data
     * @return array
     */
    public function convert($data)
    {
        foreach ($this->maps as $key => $val) {
            if( is_array($val) ) {
                //TODO
            } else {
                if( isset($data[$val]) ) {
                    $data[$key] = $data[$val];
                    unset($data[$val]);
                }
            }
        }
        foreach($this->delete as $key) {
            if( isset($data[$key]) ) {
                unset($data[$key]);
            }
        }
        return $data;
    }

}
