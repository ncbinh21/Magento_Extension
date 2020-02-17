<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 8/10/17
 * Time: 3:25 PM
 */

namespace Forix\ImportHelper\Model\Export;


interface ExportRawInterface
{
    /**
     * @return \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     */
    public function getCollection($resetCollection = false);
}