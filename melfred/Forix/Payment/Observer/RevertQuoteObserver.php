<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 07/12/2018
 * Time: 15:48
 */

namespace Forix\Payment\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
class RevertQuoteObserver implements ObserverInterface
{
    protected $_quoteRepository;
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    )
    {
        $this->_quoteRepository = $quoteRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /**
         * @var $quote \Magento\Quote\Model\Quote
         */
        if($quote = $observer->getQuote()){
            if($quote->hasDataChanges()) {
                if ($quote->getSalesOrderNo()) {
                    $this->_quoteRepository->save($observer->getQuote());
                }
            }
        }
    }
}