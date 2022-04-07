<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Model;

/**
 * File reader interface
 */
interface ReaderInterface
{
    /**
     * Read images and parse them to array
     * @return array
     */
    public function read();

}
