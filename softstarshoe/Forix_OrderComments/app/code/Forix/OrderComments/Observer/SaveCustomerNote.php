<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\OrderComments\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class SaveCustomerNote
 *
 * @package Forix\OrderComments\Observer
 */
class SaveCustomerNote implements ObserverInterface
{
    public function execute(EventObserver $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        try {
            $comment = null;
            if($information = $quote->getPayment()->getAdditionalInformation()){
                if(isset($information['comments'])){
                    $order->setCustomerNote($information['comments']);
                }
            }
        } catch (\Exception $e) {
            return;
        }
    }
}
