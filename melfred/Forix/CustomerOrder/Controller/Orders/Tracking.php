<?php

namespace Forix\CustomerOrder\Controller\Orders;

use Magento\Sales\Controller\OrderInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
/**
 * Class Manage
 * @package Forix\CustomerOrder\Controller\Orders
 */
class Tracking extends \Magento\Framework\App\Action\Action implements OrderInterface
{

    protected $resultPageFactory;
    protected $orderFactory;
    protected $_orderConvert;
    protected $_trackFactory;
    protected $_shipmentNotifier;
    protected $_customerSession;
    protected $_dataHelper;
    protected $shipment;
    /**
     * Tracking constructor.
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Convert\Order $orderConvert
     * @param \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     * @param \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory
     * @param \Forix\CustomerOrder\Helper\Data $dataHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Convert\Order $orderConvert,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Forix\CustomerOrder\Helper\Data $dataHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Shipment $shipment,
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_shipmentNotifier = $shipmentNotifier;
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_orderConvert = $orderConvert;
        $this->_trackFactory = $trackFactory;
        $this->orderFactory = $orderFactory;
        $this->_dataHelper = $dataHelper;
        $this->shipment = $shipment;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        if ($this->getRequest()->getPost('action') == 'shipment') {
            $this->manageTracking();
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        // Your code

        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function manageTracking()
    {
        $orderId = $this->_request->getParam('orderId');
        $itemId = $this->_request->getParam('itemId');
        $carrierId = $this->_request->getParam('carrierCode');
        $trackNumber = $this->_request->getParam('trackNumber');
        $qtyNumber = $this->_request->getParam('qtyNumber');
        $order = $this->orderFactory->create()->loadByIncrementId($orderId);
        $trackId = $this->_request->getParam('trackId');
        $shipmentId = $this->_request->getParam('shipmentId');
        if($order->getId()) {
            // Check if order has already shipped or can be shipped
            // Check order belong to distributor && tracking number not missing
            if (!in_array($order->getBillingAddress()->getPostcode(), $this->_dataHelper->getDistributorPostcode())) {
                $this->messageManager->addErrorMessage('You have no permision');
                return;
            }

            switch ($this->_request->getParam('act')) {
                case "add":
                    if (!preg_replace('/\s+/', '', $trackNumber) || !$order->canShip()) {
//                        $this->messageManager->addSuccessMessage('Tracking data has been updated');
                        $this->messageManager->addErrorMessage(__('QTY and Tracking Number are required'));
                        return;
                    }
                    // Initialize the order shipment object
                    $shipment = $this->_orderConvert->toShipment($order);
                    // Loop through order items
                    foreach ($order->getAllItems() AS $orderItem) {

                        //if have itemid then check if its in order
                        if ($itemId && !($itemId == $orderItem->getItemId())) {
                            continue;
                        }
                        // Check if order item is virtual or has quantity to ship
                        if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                            continue;
                        }
                        $qtyShipped = $orderItem->getQtyToShip();
                        if($itemId) {
                            if($qtyNumber) {
                                if($qtyNumber > $qtyShipped) {
                                    $this->messageManager->addErrorMessage(__('QTY input must be equal or less than %1', $qtyShipped));
                                    return;
//                                    throw new \Magento\Framework\Exception\LocalizedException(__('Qty to ship error'));
                                }
                                $qtyShipped = $qtyNumber;
                            } else {
                                $this->messageManager->addErrorMessage(__('QTY and Tracking Number are required'));
                                return;
//                                throw new \Magento\Framework\Exception\LocalizedException(__('Please input qty'));
                            }
                        }

                        // Create shipment item with qty
                        $shipmentItem = $this->_orderConvert->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                        // Add shipment item to shipment
                        $shipment->addItem($shipmentItem);
                    }
                    //check if shipment have Item
                    if (count($shipment->getAllItems())) {
                        // Register shipment
                        $shipment->register();
                        $data = array(
                            'carrier_code' => $carrierId,
                            'title' => 'Customer Tracking',
                            'number' => $trackNumber, // Replace with your tracking number
                        );
                        $shipment->getOrder()->setIsInProcess(true);
                        try {
                            // Save created shipment and order
                            $track = $this->_trackFactory->create()->addData($data);
                            $shipment->addTrack($track)->save();
                            $shipment->save();
                            $shipment->getOrder()->save();
                            // Send email
                            $this->_shipmentNotifier->notify($shipment);
                            $shipment->save();
                            $this->messageManager->addSuccessMessage('Tracking data has been updated');
                        } catch (\Exception $e) {
                            throw new \Magento\Framework\Exception\LocalizedException(
                                __($e->getMessage())
                            );
                        }
                    }
                    break;
                case "edit":
                    if ($trackId) {
                        /*** Edited by Hidro Le */
                        /* @var $track \Magento\Sales\Model\Order\Shipment\Track */
                        $tracks = $order->getTracksCollection();
                        $break = false;
                        foreach ($tracks as $track) {
                            if ($track->getId() == $trackId) {
                                $track->setNumber($trackNumber);
                                if ($track->save()) {
                                    $this->messageManager->addSuccessMessage('Tracking data has been updated');
                                }
                                break;
                            }
//                            $trackItems = $track->getShipment()->getItems();
//                            foreach ($trackItems as $trItem) {
//                                if ($trItem->getOrderItemId() == $itemId) {
//                                    $track->setNumber($trackNumber);
//                                    if ($track->save()) {
//                                        $this->messageManager->addSuccessMessage('Tracking data has been updated');
//                                    }
//                                    $break = true;
//                                    break;
//                                }
//                            }
                            /*** Can break */
                        }
                    } else {
                        if($shipmentId) {
                            $data = array(
                                'carrier_code' => $carrierId,
                                'title' => 'Customer Tracking',
                                'number' => $trackNumber, // Replace with your tracking number
                            );
                            $shipment = $this->shipment->load($shipmentId);
                            $track = $this->_trackFactory->create()->addData($data);
                            $shipment->addTrack($track)->save();
                        }
                    }
                    break;
                case "delete":
                    if ($trackId) {
                        /*** Edited by Hidro Le */
                        /* @var $track \Magento\Sales\Model\Order\Shipment\Track */
                        $tracks = $order->getTracksCollection();
                        foreach ($tracks as $track) {
//                            $trackItems = $track->getShipment()->getItems();
//                    foreach ($trackItems as $trItem) {
                            if ($track->getId() == $trackId) {
                                $track->setNumber('N/A');
                                if ($track->save()) {
                                    $this->messageManager->addSuccessMessage('Tracking data has been removed');
                                }
                                break;
                            }
//                  }
                        }
                    }
                    break;
            }
            setcookie('customer_order_id', $order->getId(), time() + (86400 * 30), '/');
        }
    }

}
