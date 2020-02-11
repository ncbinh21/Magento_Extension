<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Start Shoes
 * Date: 27/02/2018
 * Time: 14:47
 */

namespace Forix\InvoicePrint\Observer\Sales;

class ConvertQuoteToOrder extends AbstractConvert
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {

        /**
         * @var $quote \Magento\Quote\Model\Quote
         * @var $order \Magento\Sales\Model\Order
         * @var $quoteItem \Magento\Quote\Model\Quote\Item
         * @var $orderItem \Magento\Sales\Model\Order\Item
         */
        $event = $observer->getEvent();
        $quote = $event->getQuote();
        $order = $event->getOrder();
        $items = $order->getItems();
        foreach ($quote->getAllItems() as $quoteItem) {
            foreach ($items as $orderItem) {
                if($orderItem->getQuoteItemId() == $quoteItem->getId())
                    $this->convert($quoteItem, $orderItem, ['sss_product_comment']);
            }
        }
    }
}
