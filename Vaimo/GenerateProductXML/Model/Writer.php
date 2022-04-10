<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Model;


use Vaimo\GenerateProductXML\Model\Logger\CustomLogger;
use GuzzleHttp\Client;

class Writer implements WriterInterface
{

    const GENERAL_IMPORT_FOLDER = 'import/product';

    const LAYOUT = "<?xml version='1.0' encoding='utf-8'?><integrationbase></integrationbase>";

    const MAX_ITEMS_PER_FILE = 100;

    protected $xml;

    /** @var \Magento\Framework\ObjectManagerInterface  */
    private $objectManager;
    private $convert;
    private $imagick;
    private $client;

    // these attributes should be under product node
    private $priamryAttribute = [
        'sku','name','visibility','status','category','reset_website_ids',
        'links','images','docs'
    ];
    // there attributes should be under product > attribute_list
    private $attributeList = [
        'tags'
    ];

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ConvertInterface $convert,
        CustomLogger     $logger
    ) {
        $this->objectManager = $objectManager;
        $this->convert = $convert;
        $this->logger = $logger;
        $this->imagick = new \Imagick();
        $this->client = new Client(['timeout'=>30]);
    }

    /**
     * @param array $product
     * @return $this
     */
    private function generate($node, array $product)
    {
        $item = $node->addChild('product','');
        //for the xml file has pretty format.
        foreach($this->priamryAttribute as $key) {
            if( isset($product[$key]) ) {
                $funcArr = \explode(' ', ucwords(str_replace('_',' ',$key)));
                $function = 'handle'.\join('',$funcArr);
                $this->{$function}($item, $key, $product[$key]);
                unset($product[$key]);
            } else {
                $item->addChild($key,'');
            }
        }

        $attribute = $item->addChild('attributes','');
        $attribute->addAttribute('store','admin');
        foreach ($product as $key => $value) {
            if( in_array($key,$this->attributeList) ) continue;
            $funcArr = \explode(' ', ucwords(str_replace('_',' ',$key)));
            $function = 'handle'.\join('',$funcArr);
            $this->{$function}($attribute, $key, $value);
            unset($product[$key]);
        }

        $attribute_list = $item->addChild('attributes_list','')->addChild('attributes');
        foreach ($product as $key => $value) {
            $funcArr = \explode(' ', ucwords(str_replace('_',' ',$key)));
            $function = 'handle'.\join('',$funcArr);
            $this->{$function}($attribute_list, $key, $value);
        }
        return $this;
    }

    /**
     * @param $node
     * @param string $key
     * @param string $category
     * @return bool
     */
    private function handleCategory($node, string $key, string $category)
    {
        $tmp = $node->addChild('categories','')->addChild('category',htmlspecialchars($category));
        $tmp->addAttribute('root', 'Default Category');
        return true;
    }

    /**
     * @param $node
     * @param string $key
     * @param array $items
     * @return bool
     */
    private function handleColors($node, string $key, array $items)
    {
        $tmp = $node->addchild('colors','');
        foreach($items as $item) {
            $tmp1 = $tmp->addChild('color','');
            foreach($item as $key => $value) {
                $tmp1->addChild($key, htmlspecialchars($value));
            }
        }
        return true;
    }

    /**
     * @param $node
     * @param string $key
     * @param array $items
     * @return bool
     */
    private function handleTags($node, string $key, array $items)
    {
        array_walk($items, function($item) {
            $item = htmlspecialchars($item);
        });
        $node->addchild('tags',\join(',',$items));
        return true;
    }

    /**
     * @param $node
     * @param string $key
     * @param string $value
     * @return bool
     */
    private function handleImages($node, string $key, string $value)
    {
        $tmp = $node->addchild('images','');
        $tmp->addChild('image',htmlspecialchars($value));
        return true;
    }

    /**
     * @param $node
     * @param string $key
     * @param string|null $value
     * @return bool
     */
    private function handleItem($node, string $key, $value)
    {
        $value = htmlspecialchars(trim($value));
        $node->addChild($key, $value);
        return true;
    }

    /**
     * @param $method
     * @param $args
     * @return bool
     */
    public function __call($method, $args)
    {
        if( !method_exists($this, $method) ) {
            $method = 'handleItem';
        }
        return $this->{$method}($args[0],$args[1],$args[2]);
    }

    /**
     * @param array $products
     * @return int $count
     * @throws \Magento\Framework\Exception\InputException
     */
    public function write(array $products)
    {
        $prefix = $this->getXMLPath().'/product_'.date('YmdHis');
        $imagePath = $this->getImagePath();
        $count = 0;
        $total = count($products);
        foreach( \array_chunk($products, self::MAX_ITEMS_PER_FILE) as $index => $items) {
            $xmlObj = \simplexml_load_string(self::LAYOUT);
            foreach ($items as $product) {
                $product = $this->convert->convert($product);
                $this->generate($xmlObj, $product);
                $this->writeImage($product, $imagePath);
                $this->logger->addInfo(__("Write %1/%2 product with sku %3", $count++, $total, $product['sku']));
            }
            \file_put_contents($prefix . '_' . ($index+1) . '.xml', $xmlObj->asXML());
        }
        return $count;
    }

    /**
     * @param array $product
     * @param string $imagePath
     * @return bool
     */
    private function writeImage(array $product, string $imagePath )
    {
        try {
            $res = $this->client->get($product['images']);
            if( $res->getStatusCode() == 200) {
                list($imgType, $imgSuffix) = explode('/',$res->getHeader('content-type')[0]);
                if($imgType == 'image') {
                    $info = $this->getImageInfo($res->getBody());
                    if (empty($info) || strpos(\Vaimo\ImageBinder\Model\Reader::FILE_PATTERN, $info['suffix']) === false) {
                        return false;
                    }
                    $this->imagick->writeImage($imagePath.DIRECTORY_SEPARATOR.$product['sku'].'.'.$info['suffix']);
                    $this->imagick->clear();
                }
            }
        } catch (\Exception $e) {
            $this->logger->addInfo($e->getMessage());
            return false;
        }
        return true;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    private function getXMLPath()
    {
        $config = $this->objectManager->get(\Vaimo\IntegrationBase\Model\Config::class);
        $folders = [BP, $config->getBaseFilePath(), self::GENERAL_IMPORT_FOLDER];
        $path = \join(DIRECTORY_SEPARATOR, $folders);
        if( !is_dir($path) ) mkdir($path, 0777,true);
        return $path;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     */
    private function getImagePath()
    {
        $config = $this->objectManager->get(\Vaimo\ImageBinder\Model\Config::class);
        $path = $config->getAbsoluteImportPath();
        if( !is_dir($path) ) mkdir($path, 0777,true);
        return $path;
    }

    /**
     * @param $imageLink
     * @return string
     */
    private function getImageInfo($image)
    {
        $info = [];
        if( $this->imagick->readImageBlob($image) ) {
            $info['suffix'] = strtolower($this->imagick->getImageFormat());
            $info['size'] = $this->imagick->getSize();
            $info['mimeType'] = $this->imagick->getImageMimeType();
            $info['width'] = $this->imagick->getImageWidth();
            $info['height'] = $this->imagick->getImageHeight();
        }
        return $info;
    }


}
