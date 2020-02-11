<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Giftcard\Model\Giftcard\Quote;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote as ResourceQuote;

/**
 * Class Collection
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Quote
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Quote::class, ResourceQuote::class);
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
                    'giftcard_code' => 'giftcard.code',
                    'giftcard_balance' => 'giftcard.balance'
                ]
            );
        return $this;
    }
}
