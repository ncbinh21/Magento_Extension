<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 */
namespace Forix\OrderComments\Plugin\Magento\Quote\Model;

use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Model\Order\Status\HistoryRepository;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory;

class QuoteManagement
{
    /** @var \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory */
    protected $historyFactory;
    /** @var \Magento\Sales\Model\OrderFactory $orderFactory */
    protected $orderFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var HistoryRepository
     */
    protected $historyRepository;

    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Model\AccountManagement
     */
    protected $accountManagement;

    /**
     * QuoteManagement constructor.
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param CollectionFactory $collectionFactory
     * @param HistoryRepository $historyRepository
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     */
    public function __construct(
        \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $collectionFactory,
        \Magento\Sales\Model\Order\Status\HistoryRepository $historyRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\AccountManagement $accountManagement
    )
    {
        $this->historyFactory = $historyFactory;
        $this->collectionFactory = $collectionFactory;
        $this->historyRepository = $historyRepository;
        $this->orderFactory = $orderFactory;
        $this->subscriberFactory = $subscriberFactory;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->accountManagement = $accountManagement;
    }

    public function aroundSubmit(\Magento\Quote\Model\QuoteManagement $subject, $proceed, $quote, $orderData = [])
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $isSubcriber = $this->accountManagement->isEmailAvailable($quote->getCustomerEmail(), $websiteId);
        $returnValue = $proceed($quote, $orderData);
        if($isSubcriber && isset($_COOKIE['is_subscribe']) && $_COOKIE['is_subscribe'] == 1) {
            $this->subscriberFactory->create()->subscribe($quote->getCustomerEmail());
        }
        $paymentAdditionalInformation = $quote->getPayment()->getAdditionalInformation();
        if ($returnValue) {
            $order = $returnValue;
            if ($order->getData('entity_id')) {
                /** @param string $status */
                $status = $order->getData('status');
                /** @param \Magento\Sales\Model\Order\Status\HistoryFactory $history */
                $historyOrderNote = $this->historyFactory->create();
                // set comment history data order note
                $historyOrderNote->setData('comment', '');
                if (isset($paymentAdditionalInformation['comments']) && $paymentAdditionalInformation['comments']) {
                    $historyOrderNote->setData('comment', strip_tags($paymentAdditionalInformation['comments']));
                }
                $historyOrderNote->setData('parent_id', $order->getData('entity_id'));
                $historyOrderNote->setData('is_visible_on_front', 1);
                $historyOrderNote->setData('is_order_note', 1);
                $historyOrderNote->setData('is_customer_notified', 0);
                $historyOrderNote->setData('entity_name', 'order');
                $historyOrderNote->setData('status', $status);
                $historyOrderNote->save();

                $historyPoNumber = $this->historyFactory->create();
                // set comment history data po number
                $historyPoNumber->setData('comment', '');
                if (isset($paymentAdditionalInformation['ponumber']) && $paymentAdditionalInformation['ponumber']) {
                    $historyPoNumber->setData('comment', strip_tags($paymentAdditionalInformation['ponumber']));
                }
                $historyPoNumber->setData('parent_id', $order->getData('entity_id'));
                $historyPoNumber->setData('is_visible_on_front', 1);
                $historyPoNumber->setData('is_po_number', 1);
                $historyPoNumber->setData('is_customer_notified', 0);
                $historyPoNumber->setData('entity_name', 'order');
                $historyPoNumber->setData('status', $status);
                $historyPoNumber->save();
            }
        }
        return $returnValue;
    }
}