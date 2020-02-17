<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\CustomerOrder\Controller\Orders;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\TestFramework\Event\Magento;

class ChangeStatus extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var Context|\Magento\Sales\Model\Service\InvoiceService
     */
    protected $invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $transaction;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $convertOrder;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\TrackFactory
     */
    protected $trackFactory;

    /**
     * ChangeStatus constructor.
     * @param Context $context
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Convert\Order $convertOrder
     * @param \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory
     */
    public function __construct(
        Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transaction = $transaction;
        $this->orderFactory = $orderFactory;
        $this->convertOrder = $convertOrder;
        $this->trackFactory = $trackFactory;
        parent::__construct($context);
    }

    /**
     * Unset persistent cookie action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (isset($params['order_status_exe'])) {
            try {
                $order = $this->orderFactory->create();
                $order->loadByIncrementId($this->orderRepository->get($params['order_id_exe'])->getIncrementId());
                if ($params['order_status_exe'] == 'Processing' && $order->getState() == 'new') {
                    $this->createInvoiceOrder($order);
                    $this->messageManager->addSuccessMessage(__('Order status change successfully'));
                } elseif ($params['order_status_exe'] == 'Complete' && $order->getState() == 'new') {
                    $this->createInvoiceOrder($order);
                    $this->createShipmentOrder($order);
                    $this->messageManager->addSuccessMessage(__('Order status change successfully'));
                } elseif ($params['order_status_exe'] == 'Complete' && $order->getState() == 'processing') {
                    if($order->canInvoice()) {
                        $this->createInvoiceOrder($order);
                    }
                    if(!$order->hasShipments() && $order->canShip()) {
                        $this->createShipmentOrder($order);
                    }
                    $this->messageManager->addSuccessMessage(__('Order status change successfully'));
                } else {
                    $this->messageManager->addNoticeMessage(__('Can not change order status'));
                }
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage(__('A problem occurs when change order status'));
            }
//            $order->getTracksCollection()->getSize() > 0
        }
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('sales/orders/manage');
        return $resultRedirect;
    }

    /**
     * @param $order
     */
    protected function createInvoiceOrder($order)
    {
        if ($order->canInvoice()) {
            $invoice = $this->invoiceService->prepareInvoice($order);
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            if($order->getPayment()->getMethod() == 'magenest_sagepayus') {
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
            }
            $invoice->register();
            $invoice->getOrder()->setIsInProcess(true);
            $invoice->save();
            $transactionSave = $this->transaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $transactionSave->save();
//          $this->invoiceSender->send($invoice);
            //send notification code
            $order->addStatusHistoryComment(__('Notified customer about invoice #%1.', $invoice->getId()))
                ->setIsCustomerNotified(true)
                ->save();
        }
    }

    /**
     * @param $order
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createShipmentOrder($order)
    {
        // Check if order has already shipping or can be shipped
        if (!$order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('You can\'t create the Shipment.') );
        }

        // Initializzing Object for the order shipment
        $shipment = $this->convertOrder->toShipment($order);

        // Looping the Order Items
        foreach ($order->getAllItems() AS $orderItem) {

            // Check if the order item has Quantity to ship or is virtual
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
            $qtyShipped = $orderItem->getQtyToShip();

            // Create Shipment Item with Quantity
            $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

            // Add Shipment Item to Shipment
            $shipment->addItem($shipmentItem);
        }

        // Register Shipment
        $shipment->register();
        $data = array(
            'carrier_code' => 'distributor',
            'title' => '',
            'number' => 'N/A', // Replace with your tracking number
        );
        $shipment->getOrder()->setIsInProcess(true);
        try {
            $track = $this->trackFactory->create()->addData($data);
            $shipment->addTrack($track)->save();
            // Save created Shipment and Order
            $shipment->save();
            $shipment->getOrder()->save();

            // Send Email
            $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                ->notify($shipment);
            $shipment->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }
}
