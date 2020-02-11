<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Pool;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Code
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Pool
 */
class Code extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_giftcard_pool_code', 'id');
    }
}
