<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Payment\Observer\SalesOrder;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
/**
 * Persistent Session Observer
 */
class CreateObserver implements ObserverInterface
{
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_ERROR_RECIPIENT = 'forix_custom_checkout/distributor/email_error_sage';
    const XML_EMAIL_ERROR_SENT_TO = 'forix_custom_checkout/distributor/error_send_to';
    const URL_SAGE_100_SERVICE = 'payment/sage100_service/service_url';

    protected $sage100Factory;
    protected $_orderScheduleFactory;
    protected $_logger;
    protected $_paymentHelper;
    protected $customerOrderData;
    protected $customerFactory;
    protected $scopeConfig;
    protected $storeManager;
    protected $inlineTranslation;
    protected $sendMailDistributor;
    protected $transportBuilder;
    protected $resourceConnection;
    protected $assignInforDistributor;

    public function __construct(
        \Forix\Checkout\Observer\AssignInforDistributor $assignInforDistributor,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Forix\Checkout\Observer\SendMailDistributor $sendMailDistributor,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Forix\CustomerOrder\Helper\Data $customerOrderData,
        \Forix\Payment\Model\Sage100Factory $sage100Factory,
        \Forix\Payment\Model\OrderScheduleFactory $orderScheduleFactory,
        \Forix\Payment\Helper\PaymentHelper $paymentHelper,
        \Magento\Payment\Model\Method\Logger $logger
    ) {
        $this->assignInforDistributor = $assignInforDistributor;
        $this->resourceConnection = $resourceConnection;
        $this->transportBuilder = $transportBuilder;
        $this->sendMailDistributor = $sendMailDistributor;
        $this->inlineTranslation = $inlineTranslation;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->customerFactory = $customerFactory;
        $this->customerOrderData = $customerOrderData;
        $this->_orderScheduleFactory = $orderScheduleFactory;
        $this->_paymentHelper = $paymentHelper;
        $this->sage100Factory = $sage100Factory;
        $this->_logger = $logger;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $orders = $observer->getEvent()->getOrders();
        if (null == $orders) {
            $orders = [];
            $orders[] = $observer->getEvent()->getOrder();
        }
        /**
         * @var $orders \Magento\Sales\Model\Order[]
         */
        foreach ($orders as $order) {
            $saleOrderNo = null;
            if ($order->getId()) {
                try {
                    if($saleOrderNo = $this->assignSalesOrderNo($order)) {
                        if(!$this->createOrderSage100($order)) {
                            throw new \Exception(__('We cannot order in sage, Please try to place the order again.'));
                        }
                        $this->updateInTable($order, 'sales_order_no', $saleOrderNo, 'sales_order');
                        $this->updateInTable($order, 'sales_order_no', $saleOrderNo, 'sales_order_grid');
                    } else {
                        $this->sendMailDistributor->getDistributorWithZipcode($order);
                        $nameDistributor = $this->assignInforDistributor->getNameDistributor($order);
                        $this->updateInTable($order, 'distributor_fulfilled', 1, 'sales_order');
                        $this->updateInTable($order, 'distributor_fulfilled', 1, 'sales_order_grid');
                        if($nameDistributor) {
                            $this->updateInTable($order, 'distributor_name', $nameDistributor, 'sales_order');
                            $this->updateInTable($order, 'distributor_name', $nameDistributor, 'sales_order_grid');

                        }
                    }
                } catch (\Exception $e) {
                    $logger->info('Exception from new flow: ' . $e->getMessage());
                    $this->saveOrderStatusSage($saleOrderNo, $order->getIncrementId(), 0);
                    $emailList = $this->getConfigMail(self::XML_EMAIL_ERROR_SENT_TO);
                    $emailListArray = explode(',', $emailList);
                    if($emailListArray && count($emailListArray) > 0) {
                        foreach ($emailListArray as $email) {
                            $this->sendMailError($order, $email);
                        }
                    }
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info('Error log Localized: ' . $e->getMessage());
                }
            }
        }
    }

    public function updateInTable($order, $column, $value, $table)
    {
        $update = 'UPDATE '. $table . ' SET ' . $column . ' = "' . $value . '" where entity_id = ' . $order->getId();
        $this->resourceConnection->getConnection()->query($update);
    }

    public function checkSageExist($file)
    {
        $file_headers = @get_headers($file);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
            return false;
        }
        return true;
    }

    public function createOrderSage100($order) {
        if ($order->getSalesOrderNo()) {
            $sage100 = $this->sage100Factory->create();
            return $sage100->createSalesOrder($order);
        }
    }

    public function assignSalesOrderNo($order)
    {
        if ($order->getPayment()->getMethod() == 'magenest_sagepayus') {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($order->getPayment()->getMethod() . ' create order in sage');
            $isDistributor = false;
            if ($customerId = $order->getCustomerId()) {
                $customer = $this->customerFactory->create()->load($customerId);
                $isDistributor = $this->customerOrderData->isDistributor($customer);
            }
            if (!$isDistributor) {
                $billingAddress = $order->getBillingAddress();
                $postCode = $billingAddress->getPostcode();
                if (($postCode && $this->_paymentHelper->isMatchDistributor($postCode))) {
                    return;
                }
            }
            if(!$this->checkSageExist($this->getConfigByPath(self::URL_SAGE_100_SERVICE))) {
                throw new \Exception(__('Sage Error'));
            }
            $sage = $this->sage100Factory->create();
            if ($salesNextOrderNo = $sage->getNextSalesOrderNo()) {
                $order->setSalesOrderNo($salesNextOrderNo);
                return $salesNextOrderNo;
            }
            $logger->info('Cannot get sales order no');
            throw new \Exception(__('We cannot register Sales Order No, Please try to place the order again.'));
            return;
        }

        if ($order->getPayment()->getMethod() == 'netterms') {
            $isDistributor = false;
            if ($customerId = $order->getCustomerId()) {
                $customer = $this->customerFactory->create()->load($customerId);
                $isDistributor = $this->customerOrderData->isDistributor($customer);
            }
            $billingAddress = $order->getBillingAddress();
            $postCode = $billingAddress->getPostcode();
            if($isDistributor || ($postCode && !$this->_paymentHelper->isMatchDistributor($postCode))) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('netterms create order in sage');

                if(!$this->checkSageExist($this->getConfigByPath(self::URL_SAGE_100_SERVICE))) {
                    throw new \Exception(__('Sage Error'));
                }
                $sage = $this->sage100Factory->create();
                if ($salesNextOrderNo = $sage->getNextSalesOrderNo()) {
                    $order->setSalesOrderNo($salesNextOrderNo);
                    return $salesNextOrderNo;
                } else {
                    $logger->info('Cannot get sales order no');
                    throw new \Exception(__('We cannot register Sales Order No, Please try to place the order again.'));
                }
            }
            return;
        }
    }

    public function saveOrderStatusSage($salesOrderNo, $incrementId, $status)
    {
        $orderSchedule = $this->_orderScheduleFactory->create();
        $orderSchedule->setData(
            [
                'sales_order_no' => $salesOrderNo,
                'parent_id' => $incrementId,
                'status' => $status,
                'count' => 0
            ]
        );
        $orderSchedule->save();
    }

    public function sendMailError($order, $email)
    {
        try {
            $sender = [
                'name' => 'Melfred Borzall Store',
                'email' => 'sales@melfredborzall.com',
            ];
            $templateVars = [
                'order' => $order
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->getConfigMail(self::XML_PATH_EMAIL_ERROR_RECIPIENT)) // this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars($templateVars)
                ->setFrom($sender)
                ->addTo($email)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            return;
        }
    }

    public function getConfigMail($path)
    {
        $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $storeId = $this->storeManager->getStore()->getStoreId();
        return $this->scopeConfig->getValue($path, $scopeStore , $storeId);
    }

    public function getConfigByPath($path)
    {
        $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue($path, $scopeStore);
    }
}
