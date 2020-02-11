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

use Magento\Framework\Event\Observer;
use Mirasvit\Credit\Model\Transaction;

class Paypal extends AbstractObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Paypal\Model\Cart $cart */
        $cart = $observer->getEvent()->getData('cart');

        if ($cart) {
            $salesEntity = $cart->getSalesModel();

            if ($salesEntity instanceof \Magento\Payment\Model\Cart\SalesModel\Quote) {
                $balanceField = 'base_credit_amount_used';
            } elseif ($salesEntity instanceof \Magento\Payment\Model\Cart\SalesModel\Order) {
                $balanceField = 'base_credit_amount';
            } else {
                return;
            }

            $value = abs($salesEntity->getDataUsingMethod($balanceField));

            if ($value > 0.0001) {
                $cart->addDiscount(floatval($value));

            }
        }
    }
}
