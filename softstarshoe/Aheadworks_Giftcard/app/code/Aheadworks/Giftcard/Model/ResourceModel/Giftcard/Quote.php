<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Giftcard;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Quote
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Giftcard
 */
class Quote extends AbstractDb
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_giftcard_quote', 'id');
    }
}
