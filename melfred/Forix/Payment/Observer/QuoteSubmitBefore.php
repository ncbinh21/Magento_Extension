<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 03/12/2018
 * Time: 17:01
 */

namespace Forix\Payment\Observer;

use \Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class QuoteSubmitBefore implements ObserverInterface
{
    protected $_sage100Factory;
    protected $_customerOrderData;
    protected $_paymentHelper;
    protected $_customerRegistry;

    public function __construct(
        \Forix\CustomerOrder\Helper\Data $customerOrderData,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Forix\Payment\Helper\PaymentHelper $paymentHelper,
        \Forix\Payment\Model\Sage100Factory $sage100Factory
    )
    {
        $this->_sage100Factory = $sage100Factory;
        $this->_customerOrderData = $customerOrderData;
        $this->_paymentHelper = $paymentHelper;
        $this->_customerRegistry = $customerRegistry;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Service_Exception
     */
    public function execute(Observer $observer)
    {
        /**
         * @var $order \Magento\Sales\Model\Order
         */
        $order = $observer->getOrder();
        $quote = $observer->getQuote();

        $payment = $order->getPayment();
        if ($payment && $payment->getMethod() == 'magenest_sagepayus') {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($payment->getMethod() . ' create order in sage');
            if ($salesNextOrderNo = $quote->getSalesOrderNo()) {
                $order->setSalesOrderNo($salesNextOrderNo);
                return;
            }
            $isDistributor = false;
            if ($customerId = $order->getCustomerId()) {
                $customer = $this->_customerRegistry->retrieve($customerId);
                $isDistributor = $this->_customerOrderData->isDistributor($customer);
            }
            if (!$isDistributor) {
                $billingAddress = $order->getBillingAddress();
                $postCode = $billingAddress->getPostcode();
                if (($postCode && $this->_paymentHelper->isMatchDistributor($postCode))) {
                    return;
//                    throw new LocalizedException(__('Your billing zipcode is matching to Distributor, Please change the payment method.'));
                }
            }
            $sage = $this->_sage100Factory->create();
            $salesNextOrderNo = $sage->getNextSalesOrderNo();
            if ($salesNextOrderNo) {
                $quote->setSalesOrderNo($salesNextOrderNo);
                $order->setSalesOrderNo($salesNextOrderNo);
                return;
            }
            $logger->info('Cannot get sales order no');
            throw new LocalizedException(__('We cannot register Sales Order No, Please try to place the order again.'));
        }

        if ($payment && $payment->getMethod() == 'netterms') {

            if ($salesNextOrderNo = $quote->getSalesOrderNo()) {
                $order->setSalesOrderNo($salesNextOrderNo);
                return;
            }
            $isDistributor = false;
            if ($customerId = $order->getCustomerId()) {
                $customer = $this->_customerRegistry->retrieve($customerId);
                $isDistributor = $this->_customerOrderData->isDistributor($customer);
            }
            $billingAddress = $order->getBillingAddress();
            $postCode = $billingAddress->getPostcode();
            if($isDistributor || ($postCode && !$this->_paymentHelper->isMatchDistributor($postCode))) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('netterms create order in sage');

                $sage = $this->_sage100Factory->create();
                $salesNextOrderNo = $sage->getNextSalesOrderNo();
                if ($salesNextOrderNo) {
                    $quote->setSalesOrderNo($salesNextOrderNo);
                    $order->setSalesOrderNo($salesNextOrderNo);
                    return;
                } else {
                    $logger->info('Cannot get sales order no');
                    throw new LocalizedException(__('We cannot register Sales Order No, Please try to place the order again.'));
                }
            }
            return;
        }
    }
}