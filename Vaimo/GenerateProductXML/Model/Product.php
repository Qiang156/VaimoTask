<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Model;

use Vaimo\GenerateProductXML\Model\Logger\CustomLogger;

class Product
{

    /** @var \Vaimo\GenerateProductXML\Model\ReaderInterface $reader */
    protected $reader;

    /** @var \Vaimo\GenerateProductXML\Model\WriterInterface $writer */
    protected $writer;

    /** @var \Vaimo\GenerateProductXML\Model\ConvertInterface */
    protected $convert;

    /** @var \Vaimo\GenerateProductXML\Model\Logger\CustomLogger §logger */
    protected $logger;



    /**
     * @param ReaderInterface $reader
     * @param WriterInterface $writer
     * @param ConvertInterface $convert
     * @param CustomLogger $logger
     * $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
     */
    public function __construct(
        ReaderInterface  $reader,
        WriterInterface  $writer,
        CustomLogger     $logger
    )
    {
        $this->reader = $reader;
        $this->writer = $writer;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    public function execute($output)
    {
        $this->logger->addInfo(__("Reading images..."));
        $start = microtime(true);
        $results = $this->reader->read();

        $this->logger->addInfo(__("Finished reading products, took %1 seconds.", microtime(true) - $start));
        $this->logger->addInfo(__("%1 products found", count($results)));
        $this->logger->addInfo(__("Writing product information into XML file..."));
        $start = microtime(true);
        $count = $this->writer->write($results);
        $this->logger->addInfo(__("Finished binding %1 products, took %2 seconds.", $count, microtime(true) - $start));
    }
}
