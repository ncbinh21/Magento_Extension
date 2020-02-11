<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class AddPaymentItemObserver
 *
 * @package Aheadworks\Giftcard\Observer
 */
class AddPaymentItemObserver implements ObserverInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Payment\Model\Cart $cart */
        $cart = $observer->getEvent()->getCart();
        /** @var \Magento\Payment\Model\Cart\SalesModel\SalesModelInterface $salesModel */
        $salesModel = $cart->getSalesModel();
        $baseGiftCardAmount = $salesModel->getDataUsingMethod('base_aw_giftcard_amount');
        if ($baseGiftCardAmount > 0) {
            $cart->addDiscount((double)abs($baseGiftCardAmount));
        }
    }
}
