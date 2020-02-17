<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 10/07/2018
 * Time: 17:56
 */
namespace Forix\AdvanceShipping\Observer;
class SalesQuoteAfterLoad implements \Magento\Framework\Event\ObserverInterface
{
    protected $_registry;
    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->_registry = $registry;
    }

    /**
     * After load observer for quote
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if ($quote instanceof \Magento\Framework\Model\AbstractModel) {
            $this->_registry->register('current_quote', $quote, true);
        }
        return $this;
    }
}