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
 * @package   mirasvit/module-event
 * @version   1.1.10
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\EventData\Customer;


use Magento\Customer\Model\Customer;
use Magento\Framework\DataObject;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Sale\Collection as SaleCollection;
use Magento\Sales\Model\ResourceModel\Sale\CollectionFactory as SaleCollectionFactory;

abstract class CustomerAbstractAttribute
{
    /**
     * @var SaleCollectionFactory
     */
    private $saleCollectionFactory;

    public function __construct(SaleCollectionFactory $saleCollectionFactory)
    {
        $this->saleCollectionFactory = $saleCollectionFactory;
    }

    /**
     * Retrieve customer totals.
     *
     * @param Customer $model
     *
     * @return DataObject
     */
    final protected function getCustomerTotals(Customer $model)
    {
        /** @var SaleCollection $sale */
        $sale = $this->saleCollectionFactory->create();

        $customerTotals = $sale->setCustomerIdFilter($model->getId())
            ->setOrderStateFilter(Order::STATE_CANCELED, true)
            ->load()
            ->getTotals();

        return $customerTotals;
    }
}
