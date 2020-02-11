<?php
namespace Forix\Catalog\Observer;

use Magento\Framework\Event\ObserverInterface;

class AddAdditionalOptionOrder implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $quote = $observer->getQuote();
            $order = $observer->getOrder();
            $quoteItems = [];

            // Map Quote Item with Quote Item Id
            foreach ($quote->getAllVisibleItems() as $quoteItem) {
                $quoteItems[$quoteItem->getId()] = $quoteItem;
            }

            foreach ($order->getAllVisibleItems() as $orderItem) {
                $quoteItemId = $orderItem->getQuoteItemId();
                if(isset($quoteItems[$quoteItemId])) {
                    $quoteItem = $quoteItems[$quoteItemId];
                    $additionalOptions = $quoteItem->getOptionByCode('additional_options');

                    if (count($additionalOptions) > 0) {
                        // Get Order Item's other options
                        $options = $orderItem->getProductOptions();
                        // Set additional options to Order Item
                        $options['additional_options'] = json_decode($additionalOptions->getValue());
                        $orderItem->setProductOptions($options);
                    }
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred'));
        }
    }
}