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



namespace Mirasvit\Credit\Observer;

class InvoiceSaveAfter extends \Mirasvit\Credit\Observer\AbstractObserver
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getEvent()->getInvoice();
        $order = $invoice->getOrder();

        // is new invoice
        if ($invoice->getOrigData() === null && $invoice->getBaseCreditAmount()) {
            $order->setBaseCreditInvoiced($order->getBaseCreditInvoiced() + $invoice->getBaseCreditAmount())
                ->setCreditInvoiced($order->getCreditInvoiced() + $invoice->getCreditAmount());
        }

        $order->getResource()->saveAttribute($order, 'base_credit_invoiced');
        $order->getResource()->saveAttribute($order, 'credit_invoiced');
    }
}
