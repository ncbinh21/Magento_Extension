<?php

namespace Forix\OrderComments\Controller\Adminhtml\Order;

use Magento\Framework\App\Action\Context;

class UpdateNoteAjax extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Api\OrderStatusHistoryRepositoryInterface
     */
    protected $historyRepository;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * RemoveAjax constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $collectionFactory,
        \Magento\Sales\Api\OrderStatusHistoryRepositoryInterface $historyRepository
    ) {
        $this->historyRepository = $historyRepository;
        $this->collectionFactory = $collectionFactory;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * execute
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');

        $customerNote = $this->getRequest()->getParam('customer_note');
        $checkType = $this->getRequest()->getParam('check_type');

        $order = $this->orderRepository->get($orderId);
        switch ($checkType) {
            case 'is_order_note':
                $order->setOrderNote($customerNote);
                break;
            case 'is_po_number':
                $order->setPoNumber($customerNote);
                break;
            default:
                break;
        }

        $order->save();

        $historyCollection = $this->collectionFactory->create();
        $historyCollection->addFieldToFilter($checkType, 1)
            ->addFieldToFilter('parent_id', $orderId);

        $historyRepositor = $this->historyRepository->get($historyCollection->getFirstItem()->getId());
        $historyRepositor->setComment($customerNote);

        $historyRepositor->save();

        $response = [
            'success' => true,
        ];

        $this->getResponse()->representJson(
            $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
        );
    }
}