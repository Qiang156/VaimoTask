<?php
/**
 * Copyright © 2009-2016 Vaimo Group. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Vaimo\GenerateProductXML\Model;

/**
 * File reader interface
 */
interface ConvertInterface
{
    /**
     * convert array with other information such as key word.
     * @return array
     */
    public function convert($data);

}
