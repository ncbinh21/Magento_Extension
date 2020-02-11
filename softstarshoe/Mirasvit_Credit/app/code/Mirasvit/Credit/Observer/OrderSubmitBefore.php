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

class OrderSubmitBefore extends AbstractObserver
{
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $address = $observer->getEvent()->getAddress();
        if (!$address) {
            $quote = $observer->getEvent()->getQuote();
            if ($quote->getIsVirtual()) {
                $address = $quote->getBillingAddress();
            } else {
                $address = $quote->getShippingAddress();
            }
        }

        if ($address) {
            $order->setBaseCreditAmount($address->getBaseCreditAmount())
                ->setCreditAmount($address->getCreditAmount());
        }
    }
}
