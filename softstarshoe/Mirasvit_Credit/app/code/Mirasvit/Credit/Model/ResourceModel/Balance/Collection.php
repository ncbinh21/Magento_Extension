<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Model\ResourceModel\Balance;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\DB\Select;

class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\Credit\Model\Balance', 'Mirasvit\Credit\Model\ResourceModel\Balance');
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $this->joinCustomer();

        return $this;
    }

    /**
     * @return $this
     */
    protected function joinCustomer()
    {
        $nameExpr = new \Zend_Db_Expr('CONCAT(customer.firstname, " ", customer.lastname)');

        $this->getSelect()->joinLeft(
            ['customer' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            ['email' => 'email', 'name' => $nameExpr]
        );

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalBalance()
    {
        $this->_renderFilters();

        $select = clone $this->getSelect();
        $select->reset(Select::ORDER)
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET)
            ->reset(Select::COLUMNS)
            ->columns('SUM(amount)');

        return $this->getConnection()->fetchOne($select);
    }
}
