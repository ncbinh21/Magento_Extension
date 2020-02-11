<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Giftcard\Model\Giftcard\Order;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Order as ResourceOrder;

/**
 * Class Collection
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Order
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Order::class, ResourceOrder::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinLeft(
                ['giftcard' => $this->getTable('aw_giftcard')],
                'main_table.giftcard_id = giftcard.id',
                [
                    'giftcard_code' => 'giftcard.code'
                ]
            );
        $this->addFilterToMap('order_id', 'main_table.order_id');
        return $this;
    }
}
