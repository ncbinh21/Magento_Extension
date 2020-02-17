<?php

namespace Forix\CatalogImport\Model\Order;

use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;

class CreateTrackingNumber extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    protected $_eventManager;
    protected $_productCollectionFactory;
    protected $dateTime;
    protected $_replaceFlag;
    protected $orderCollectionFactory;
    protected $convertOrder;
    protected $logger;
    protected $trackFactory;
    protected $objectManager;
    protected $invoiceService;
    protected $transaction;
    protected $invoiceSender;

    const COLUMN_ORDER_NO = 'Order_No';
    const COLUMN_TRACKING_NUMBER = 'Tracking_Number';
    const COLUMN_ITEMS = 'Items';
    const ERROR_ORDER_NO_IS_EMPTY = 'orderNo';

    public function __construct(
        \Magento\Sales\Model\Order\Email\Sender\InvoiceSender $invoiceSender,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\Order\Shipment\TrackFactory $trackFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        ProcessingErrorAggregatorInterface $errorAggregator
    )
    {
        $this->transaction = $transaction;
        $this->invoiceSender = $invoiceSender;
        $this->invoiceService = $invoiceService;
        $this->objectManager = $objectManager;
        $this->trackFactory = $trackFactory;
        $this->logger = $logger;
        $this->convertOrder = $convertOrder;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->_eventManager = $eventManager;
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;
        $this->_productCollectionFactory = $collectionFactory;
        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }

        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
    }

    protected function _saveValidatedBunches()
    {
        $source = $this->_getSource();
        $currentDataSize = 0;
        $bunchRows = [];
        $startNewBunch = false;
        $nextRowBackup = [];
        $maxDataSize = $this->_resourceHelper->getMaxDataSize();
        $bunchSize = $this->_importExportData->getBunchSize();

        $source->rewind();
        $this->_dataSourceModel->cleanBunches();

        while ($source->valid() || $bunchRows) {
            if ($startNewBunch || !$source->valid()) {
                $this->_dataSourceModel->saveBunch($this->getEntityTypeCode(), $this->getBehavior(), $bunchRows);
                $bunchRows = $nextRowBackup;
                $currentDataSize = strlen(json_encode($bunchRows));
                $startNewBunch = false;
                $nextRowBackup = [];
            }
            if ($source->valid()) {
                try {
                    $rowData = $source->current();
                } catch (\InvalidArgumentException $e) {
                    $this->addRowError($e->getMessage(), $this->_processedRowsCount);
                    $this->_processedRowsCount++;
                    $source->next();
                    continue;
                }

                $this->_processedRowsCount++;

                if ($this->validateRow($rowData, $source->key())) {
                    // add row to bunch for save
                    $rowData = $this->_prepareRowForDb($rowData);
                    $rowSize = strlen($this->jsonHelper->jsonEncode($rowData));

                    $isBunchSizeExceeded = $bunchSize > 0 && count($bunchRows) >= $bunchSize;

                    if ($currentDataSize + $rowSize >= $maxDataSize || $isBunchSizeExceeded) {
                        $startNewBunch = true;
                        $nextRowBackup = [$source->key() => $rowData];
                    } else {
                        $bunchRows[$source->key()] = $rowData;
                        $currentDataSize += $rowSize;
                    }
                }
                $source->next();
            }
        }
        return $this;
    }


    /**
     * Import data rows.
     *
     * @return boolean
     */
    protected function _importData()
    {
        $this->_validatedRows = null;
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            //
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            //
        } else {
            $this->createTrackingNumberProcess();
        }
        return true;
    }

    public function createTrackingNumberProcess()
    {
        try {
            while ($bunch = $this->_dataSourceModel->getNextBunch()) {
                // action
                $orderNo = [];
                foreach ($bunch as $rowNum => $rowData) {
                    $orderNo[$rowData[self::COLUMN_ORDER_NO]] = $rowData;
                }
                $orderNos = array_keys($orderNo);
                $orderCollection = $this->orderCollectionFactory->create();
                $orderCollection->addFieldToFilter('sales_order_no', ['in' => $orderNos]);
                foreach ($orderCollection as $order) {
                    foreach ($bunch as $rowData) {
                        if($rowData[self::COLUMN_ORDER_NO] == $order->getSalesOrderNo() && $rowData[self::COLUMN_TRACKING_NUMBER]){
                            if($this->createShipmentOrder($order, $rowData[self::COLUMN_TRACKING_NUMBER], $rowData[self::COLUMN_ITEMS])) {
                                $this->createInvoiceOrder($order, $rowData[self::COLUMN_ITEMS]);
                            }
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sageOrderImport.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Error import');
            $logger->info('Message: '. $exception->getMessage());
        }
    }

    /**
     * @param $order
     * @param $allSkuAndQty
     */
    protected function createInvoiceOrder($order, $allSkuAndQty) {
        if (!$order->canInvoice()) {
            $this->logger->error(__("Order can't ship at order id %1", $order->getId()));
            return true;
        }
        $itemsArray = [];
        foreach ($order->getAllVisibleItems() AS $orderItem) {
            $skuAndQty = explode(',', $allSkuAndQty);
            foreach ($skuAndQty as $item) {
                $check = explode(';', $item);
                if(isset($check[0]) && isset($check[1]) && count($check) == 2 && $orderItem->getSku() == $check[0]) {
                    $itemsArray[$orderItem->getId()] = $check[1];
                }
            }
        }
        try {
            if (!empty($itemsArray)) {
                $invoice = $this->invoiceService->prepareInvoice($order, $itemsArray);
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                if($order->getPayment()->getMethod() == 'magenest_sagepayus') {
                    $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
                }
                $invoice->register();
                $transactionSave = $this->transaction->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();
                $this->invoiceSender->send($invoice);
                //send notification code
                $order->addStatusHistoryComment(__('Notified customer about invoice #%1.', $invoice->getId()))->setIsCustomerNotified(true)->save();
            }
        } catch (\Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sageOrderImport.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('Error create invoice order id = ' . $order->getId());
            $logger->info('Message: '. $e->getMessage());
            return null;
            throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
            );
        }
    }


    /**
     * @param $order
     * @param $trackingNumber
     * @param $allSkuAndQty
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function createShipmentOrder($order, $trackingNumber, $allSkuAndQty)
    {
        // Check if order has already shipping or can be shipped
        if (!$order->canShip()) {
            $this->logger->error(__("Order can't ship at order id %1", $order->getId()));
            return false;
        }

        // Initializzing Object for the order shipment
        $shipment = $this->convertOrder->toShipment($order);
        $flag = false;
        // Looping the Order Items
        foreach ($order->getAllVisibleItems() AS $orderItem) {
            $skuAndQty = explode(',', $allSkuAndQty);
            foreach ($skuAndQty as $item) {
                $check =  explode(';', $item);
                if(isset($check[0]) && isset($check[1]) && count($check) == 2 && $orderItem->getSku() == $check[0]) {
                    // Check if the order item has Quantity to ship or is virtual
                    if ($orderItem->getQtyToShip() < $check[1] || $orderItem->getIsVirtual()) {
                        $this->logger->error(__("Item %1 can't ship at order id %2", $orderItem->getName(), $order->getId()));
                        return false;
                    }
//                    if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
//                        continue;
//                    }

                    $qtyShipped = $orderItem->getQtyToShip();

                    // Create Shipment Item with Quantity
                    $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($check[1]);

                    // Add Shipment Item to Shipment
                    $shipment->addItem($shipmentItem);
                    $flag = true;
                    break;
                }
            }
        }
        // Register Shipment
        if($flag) {
            $shipment->register();
            $carrierCode = 'custom';
            $carrierArray = explode('_', $order->getShippingMethod());
            if (isset($carrierArray[0])) {
                $carrierCode = $carrierArray[0];
            }
            $data = array(
                'carrier_code' => $carrierCode,
                'title' => 'Sage Import Tracking',
                'number' => $trackingNumber, // Replace with your tracking number
            );
            $shipment->getOrder()->setIsInProcess(true);
            try {
                $track = $this->trackFactory->create()->addData($data);
                $shipment->addTrack($track)->save();
                // Save created Shipment and Order
                $shipment->save();
                $shipment->getOrder()->save();

                // Send Email
                $this->objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                    ->notify($shipment);
                $shipment->save();
                return true;
            } catch (\Exception $e) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sageOrderImport.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('Error create shipment order id = ' . $order->getId());
                $logger->info('Message: ' . $e->getMessage());
                return null;
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }
        }
        return false;
    }
    /**
     * Validate data
     *
     * @return ProcessingErrorAggregatorInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData()
    {
        if (!$this->_dataValidated) {
            $this->getErrorAggregator()->clear();
            // do all permanent columns exist?
            $absentColumns = array_diff($this->_permanentAttributes, $this->getSource()->getColNames());
            $this->addErrors(self::ERROR_CODE_COLUMN_NOT_FOUND, $absentColumns);

            // check attribute columns names validity
            $columnNumber = 0;
            $emptyHeaderColumns = [];
            $invalidColumns = [];
            $invalidAttributes = [];
            foreach ($this->getSource()->getColNames() as $columnName) {
                $columnNumber++;
                if (!$this->isAttributeParticular($columnName)) {
                    if (trim($columnName) == '') {
                        $emptyHeaderColumns[] = $columnNumber;
                    } elseif ($this->needColumnCheck && !in_array($columnName, $this->getValidColumnNames())) {
                        $invalidAttributes[] = $columnName;
                    }
                }
            }
            $this->addErrors(self::ERROR_CODE_INVALID_ATTRIBUTE, $invalidAttributes);
            $this->addErrors(self::ERROR_CODE_COLUMN_EMPTY_HEADER, $emptyHeaderColumns);
            $this->addErrors(self::ERROR_CODE_COLUMN_NAME_INVALID, $invalidColumns);

            if (!$this->getErrorAggregator()->getErrorsCount()) {
                $this->_saveValidatedBunches();
                $this->_dataValidated = true;
            }
        }
        return $this->getErrorAggregator();
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'melfred_create_tracking_number';
    }

    /**
     * Validate data row.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            // check that row is already validated
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        if (isset($rowData[self::COLUMN_ORDER_NO]) && isset($rowData[self::COLUMN_TRACKING_NUMBER]) && isset($rowData[self::COLUMN_ITEMS])) {
            if ('' === $rowData[self::COLUMN_ORDER_NO]) {
                $this->addRowError(SELF::ERROR_ORDER_NO_IS_EMPTY, $rowNum);
            }
        } else {
            $this->addRowError(SELF::ERROR_ORDER_NO_IS_EMPTY, $rowNum);
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
}