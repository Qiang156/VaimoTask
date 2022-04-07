<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Model;

interface WriterInterface
{
    /**
     * write a product into xml file
     * @return boolean
     */
    public function write(array $product);

}
