<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Model;


use Vaimo\GenerateProductXML\Model\Logger\CustomLogger;

class Writer implements WriterInterface
{

    const GENERAL_IMPORT_FOLDER = 'import/product';

    const LAYOUT = "<?xml version='1.0' encoding='utf-8'?><integrationbase></integrationbase>";

    const MAX_ITEMS_PER_FILE = 10;

    protected $xml;

    /** @var \Magento\Framework\ObjectManagerInterface  */
    private $objectManager;
    private $convert;
    private $imagick;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ConvertInterface $convert,
        CustomLogger     $logger
    ) {
        $this->objectManager = $objectManager;
        $this->convert = $convert;
        $this->logger = $logger;
        $this->imagick = new \Imagick();
    }

    /**
     * @param array $product
     * @return $this
     */
    private function generate($node, array $product)
    {
        $item = $node->addChild('product','');
        foreach ($product as $key => $value) {
            switch ($key) {
                case 'category':
                    $this->handleCategory($item, $value);
                    break;
                case 'images':
                    $this->handleImages($item, $value);
                    break;
                case 'colors':
                    $this->handleColor($item, $value);
                    break;
                case 'tags':
                    $this->handleTags($item, $value);
                    break;
                default:
                    $this->handleItem($item, $key, $value);
            }
        }
        return $this;
    }

    /**
     * @param $node
     * @param $category
     * @return bool
     */
    private function handleCategory($node, $category)
    {
        $tmp = $node->addChild('categories','')->addChild('category',htmlspecialchars($category));
        $tmp->addAttribute('root', 'Default Category');
        return true;
    }

    /**
     * @param $node
     * @param $items
     * @return bool
     */
    private function handleColor($node, $items)
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
     * @param $items
     * @return bool
     */
    private function handleTags($node, $items)
    {
        $tmp = $node->addchild('tags','');
        foreach($items as $item) {
            $tmp->addChild('tag',htmlspecialchars($item));
        }
        return true;
    }

    /**
     * @param $node
     * @param $items
     * @return bool
     */
    private function handleImages($node, $value)
    {
        $tmp = $node->addchild('images','');
        $tmp->addChild('image',htmlspecialchars($value));
        return true;
    }

    /**
     * @param $node
     * @param $key
     * @param $value
     * @return bool
     */
    private function handleItem($node, $key, $value)
    {
        $value = htmlspecialchars(trim($value));
        $node->addChild($key, $value);
        return true;
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
     * @param string $imagefile
     * @return bool
     * @throws \ImagickException
     */
    private function writeImage( array $product, string $path )
    {
        try {
            $info = $this->getImageInfo($product['images']);
            if( strpos(\Vaimo\ImageBinder\Model\Reader::FILE_PATTERN, $info['suffix'] ) === false ) {
                return false;
            }
            $this->imagick->writeImage($path.'/'.$product['sku'].'.'.$info['suffix']);
            $this->imagick->clear();
        } catch(\Exception $e) {
            $this->logger($e->getMessage());
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
    private function getImageInfo($imageLink)
    {
        $info = [];
        $this->imagick->readImage($imageLink);
        $info['suffix'] = strtolower( $this->imagick->getImageFormat() );
        $info['size'] = $this->imagick->getSize();
        $info['mimeType'] = $this->imagick->getImageMimeType();
        $info['width'] = $this->imagick->getImageWidth();
        $info['height'] = $this->imagick->getImageHeight();
        return $info;
    }


}
