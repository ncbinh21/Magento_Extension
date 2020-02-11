<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Pool;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Giftcard\Model\Pool;
use Aheadworks\Giftcard\Model\ResourceModel\Pool as ResourcePool;

/**
 * Class Collection
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Pool
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Pool::class, ResourcePool::class);
    }
}
