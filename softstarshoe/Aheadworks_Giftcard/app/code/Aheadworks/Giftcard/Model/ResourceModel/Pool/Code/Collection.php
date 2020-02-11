<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Pool\Code;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Giftcard\Model\Pool\Code;
use Aheadworks\Giftcard\Model\ResourceModel\Pool\Code as ResourceCode;

/**
 * Class Collection
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Pool\Code
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
        $this->_init(Code::class, ResourceCode::class);
    }
}
