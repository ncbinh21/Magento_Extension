<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Giftcard;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class History
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel
 */
class History extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_giftcard_history', 'id');
    }
}
