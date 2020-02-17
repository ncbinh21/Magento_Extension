<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\Api\Observer\Customer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;

class SaveCustomer implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $customer = $observer->getCustomer();
        return $this;
    }
}