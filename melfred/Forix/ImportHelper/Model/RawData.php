<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 4:26 PM
 */

namespace Forix\ImportHelper\Model;

class RawData extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\ImportHelper\Model\ResourceModel\RawData');
    }
}