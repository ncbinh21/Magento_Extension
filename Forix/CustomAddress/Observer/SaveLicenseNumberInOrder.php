<?php
namespace Forix\CustomAddress\Observer;

class SaveLicenseNumberInOrder implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        if ($quote->getBillingAddress()) {
            $order->getBillingAddress()->setLicenseNumber($quote->getBillingAddress()->getLicenseNumber());
        }
        if (!$quote->isVirtual()) {
            $order->getShippingAddress()->setLicenseNumber($quote->getShippingAddress()->getLicenseNumber());
        }
        return $this;
    }
}