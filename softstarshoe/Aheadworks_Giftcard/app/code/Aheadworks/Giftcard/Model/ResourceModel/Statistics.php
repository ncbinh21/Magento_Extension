<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Statistics
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel
 */
class Statistics extends AbstractDb
{
    /**
     * @var int|null
     */
    private $storeId;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('aw_giftcard_statistics', 'id');
    }

    /**
     * Set store id
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Check whether exists statistics with given $productId and $storeId
     *
     * @param int $productId
     * @param int $storeId
     * @return bool
     */
    public function existsStatistics($productId, $storeId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), 'id')
            ->where('product_id = ?', $productId)
            ->where('store_id = ?', $storeId);

        if ($connection->fetchOne($select) === false) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($this->storeId !== null) {
            $select->where('store_id = ?', $this->storeId);
        }
        $this->storeId = null;
        return $select;
    }
}
