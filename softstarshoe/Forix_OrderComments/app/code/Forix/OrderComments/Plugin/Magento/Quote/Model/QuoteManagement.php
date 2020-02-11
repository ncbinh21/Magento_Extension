<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 */
namespace Forix\OrderComments\Plugin\Magento\Quote\Model;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Sales\Model\Order\Status\HistoryRepository;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory;

class QuoteManagement {
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
     * QuoteManagement constructor.
     * @param \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory
     * @param CollectionFactory $collectionFactory
     * @param HistoryRepository $historyRepository
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     */
    public function __construct(
        \Magento\Sales\Model\Order\Status\HistoryFactory $historyFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $collectionFactory,
        \Magento\Sales\Model\Order\Status\HistoryRepository $historyRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->historyFactory = $historyFactory;
        $this->collectionFactory = $collectionFactory;
        $this->historyRepository = $historyRepository;
        $this->orderFactory = $orderFactory;
    }
    public function aroundSubmit(\Magento\Quote\Model\QuoteManagement $subject, $proceed, $quote, $orderData = [])
    {
        $returnValue = $proceed($quote, $orderData);
        $paymentAdditionalInformation = $quote->getPayment()->getAdditionalInformation();
        if ($returnValue) {
            $order = $returnValue;
            if ($order->getData('entity_id')) {
                if(isset($paymentAdditionalInformation['comments'])) {
                    if(!$paymentAdditionalInformation['comments'] && $quote->getCustomerNote()) {
                        if(!$order->getCustomerNote()) {
                            $order->setCustomerNote($quote->getCustomerNote());
                            $order->save();
                        }
                    }
                }
                /** @param string $status */
                $status = $order->getData('status');
                /** @param \Magento\Sales\Model\Order\Status\HistoryFactory $history */
                $history = $this->historyFactory->create();
                $historyCollection = $this->collectionFactory->create();
                $historyItem = $historyCollection->addFieldToFilter('parent_id', $order->getData('entity_id'))->getFirstItem();
                if($historyItem->getId() && $historyItem->getIsCustomerNotified() !== null) {
                    $historyData = $this->historyRepository->get($historyItem->getId());
                    $historyData->setData('is_visible_on_front', 1);
                    $historyData->setData('is_order_note', 1);
                    $historyData->save();
                } else {
                    // set comment history data
                    $history->setData('comment', '');
                    if(isset($paymentAdditionalInformation['comments']) && $paymentAdditionalInformation['comments']) {
                        $history->setData('comment', strip_tags($paymentAdditionalInformation['comments']));
                    }
                    elseif ($quote->getCustomerNote()) {
                        $history->setData('comment', strip_tags($quote->getCustomerNote()));
                    }
                    $history->setData('parent_id', $order->getData('entity_id'));
                    $history->setData('is_visible_on_front', 1);
                    $history->setData('is_order_note', 1);
                    $history->setData('is_customer_notified', 0);
                    $history->setData('entity_name', 'order');
                    $history->setData('status', $status);
                    $history->save();
                }
            }

        }
        return $returnValue;
    }
}