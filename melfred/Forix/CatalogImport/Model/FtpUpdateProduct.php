<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 15/11/2018
 * Time: 19:19
 */

namespace Forix\CatalogImport\Model;


use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use \Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;

;

class FtpUpdateProduct extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    protected $_eventManager;
    protected $_productCollectionFactory;
    protected $_stockResItemFac;
    protected $_stockConfiguration;
    protected $_stockStateProvider;
    protected $dateTime;
    protected $indexerRegistry;
    protected $_replaceFlag;
    protected $indexerFactory;
    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $_stockRegistry;

    const COLUMN_ITEM_CODE = 'item_code';
    const COLUMN_STANDARD_UNIT_PRICE = 'standard_unit_price';
    const COLUMN_TOTAL_QUANTITY_ON_HAND = 'total_quantity_on_hand';
    const CUSTOM_FIELDS_MAP = [
        'item_code' => 'Item Code',
        'standard_unit_price' => 'StandardUnitPrice',
        'total_quantity_on_hand' => 'TotalQuantityOnHand',
    ];


    /**
     * @var array
     */
    protected $defaultStockData = [
        'manage_stock' => 1,
        'use_config_manage_stock' => 1,
        'qty' => 0,
        'min_qty' => 0,
        'use_config_min_qty' => 1,
        'min_sale_qty' => 1,
        'use_config_min_sale_qty' => 1,
        'max_sale_qty' => 10000,
        'use_config_max_sale_qty' => 1,
        'is_qty_decimal' => 0,
        'backorders' => 0,
        'use_config_backorders' => 1,
        'notify_stock_qty' => 1,
        'use_config_notify_stock_qty' => 1,
        'enable_qty_increments' => 0,
        'use_config_enable_qty_inc' => 1,
        'qty_increments' => 0,
        'use_config_qty_increments' => 1,
        'is_in_stock' => 1,
        'low_stock_date' => null,
        'stock_status_changed_auto' => 0,
        'is_decimal_divided' => 0,
    ];


    protected $_messageTemplates = [
        ValidatorInterface::ERROR_SKU_IS_EMPTY => 'SKU is empty',
    ];

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    )
    {
        $this->indexerFactory = $indexerFactory;
        $this->_eventManager = $eventManager;
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;
        $this->_productCollectionFactory = $collectionFactory;
        $this->_stockRegistry = $stockRegistry;
        $this->_stockResItemFac = $stockResItemFac;
        $this->_stockConfiguration = $stockConfiguration;
        $this->_stockStateProvider = $stockStateProvider;
        $this->dateTime = $dateTime;
        $this->indexerRegistry = $indexerRegistry;
        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }

        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
    }

    protected function doDataFilter($data)
    {
        $this->convertData($data, $this::CUSTOM_FIELDS_MAP);
        return $data;
    }

    protected function convertData(&$data, $map)
    {
        foreach ($map as $to => $from) {
            if (isset($data[$from])) {
                $customValue = trim($data[$from]);
                $data[$to] = $customValue;
                unset($data[$from]);
            }
        }
        return $data;
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

                $rowData = $this->doDataFilter($rowData);

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
        if (Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->_deleteProducts();
        } elseif (Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->_replaceFlag = true;
            $this->_replaceProducts();
        } else {
            $this->_saveProductsData();
        }
        $this->_eventManager->dispatch('catalog_product_import_finish_before', ['adapter' => $this]);
        return true;
    }

    public function _deleteProducts()
    {
        //Do-nothing
    }

    public function _replaceProducts()
    {
        //Do-nothing
    }

    public function _saveProductsData()
    {
        $stockResource = $this->_stockResItemFac->create();
        $entityTable = $stockResource->getMainTable();
        $productIdsToReindex = [];
        try {
            while ($bunch = $this->_dataSourceModel->getNextBunch()) {
                $skuData = [];
                $stockData = [];
                foreach ($bunch as $rowNum => $rowData) {
                    $skuData[$rowData[self::COLUMN_ITEM_CODE]] = $rowData;
                }
                $skus = array_keys($skuData);
                $productCollection = $this->_productCollectionFactory->create();
                $productCollection->addFieldToFilter('sku', ['in' => $skus]);

                /**
                 * @var $product \Magento\Catalog\Model\Product
                 */
                foreach ($productCollection as $product) {
                    if (!in_array($product->getEntityId(), $productIdsToReindex)) {
                        $productIdsToReindex[] = $product->getEntityId();
                    }

                    // ----------------------------------- Update Product Price
                    if (isset($skuData[$product->getSku()]) && '' !== $skuData[$product->getSku()] && '' !== $skuData[$product->getSku()][self::COLUMN_STANDARD_UNIT_PRICE]) {
                        $product->setPrice($skuData[$product->getSku()][self::COLUMN_STANDARD_UNIT_PRICE]);
                        $product->getResource()->saveAttribute($product, 'price');
                    }
                    // ----------------------------------- Update Product Price

                    // ----------------------------------- Update Product Qty
                    if (isset($skuData[$product->getSku()]) && '' !== $skuData[$product->getSku()] && '' !== $skuData[$product->getSku()][self::COLUMN_TOTAL_QUANTITY_ON_HAND]) {
                        $row = [];
                        $sku = $product->getSku();

                        $row['qty'] = $skuData[$product->getSku()][self::COLUMN_TOTAL_QUANTITY_ON_HAND];
                        $row['product_id'] = $product->getEntityId();
                        $row['website_id'] = $this->_stockConfiguration->getDefaultScopeId();
                        $row['stock_id'] = $this->_stockRegistry->getStock($product->getWebsiteIds()[0])->getStockId();

                        $stockItemDo = $this->_stockRegistry->getStockItem($row['product_id'], $row['website_id']);
                        $existStockData = $stockItemDo->getData();

                        $row = array_merge(
                            $this->defaultStockData,
                            array_intersect_key($existStockData, $this->defaultStockData),
                            $row
                        );

                        if ($this->_stockConfiguration->isQty($product->getTypeId())
                        ) {
                            $stockItemDo->setData($row);
                            $row['is_in_stock'] = $this->_stockStateProvider->verifyStock($stockItemDo);
                            if ($this->_stockStateProvider->verifyNotification($stockItemDo)) {
                                $row['low_stock_date'] = $this->dateTime->gmDate(
                                    'Y-m-d H:i:s',
                                    (new \DateTime())->getTimestamp()
                                );
                            }
                            $row['stock_status_changed_auto'] =
                                (int)!$this->_stockStateProvider->verifyStock($stockItemDo);
                        } else {
                            $row['qty'] = 0;
                        }

                        if (!isset($stockData[$sku])) {
                            $stockData[$sku] = $row;
                        }
                    }
                    // ----------------------------------- Update Product Qty
                }
                // Insert rows
                if (!empty($stockData)) {
                    $this->_connection->insertOnDuplicate($entityTable, array_values($stockData));
                }
            }
        } catch (\Exception $exception) {
            $exception->getMessage();
        }
//        $this->reindexProducts();
//        $this->reindexProductAll();
    }

    public function reindexProductAll() {
        $indexerIds = array(
            'catalog_category_product',
            'catalog_product_price',
            'catalog_product_flat'
        );
        foreach ($indexerIds as $indexerId) {
            $idx = $this->indexerFactory->create()->load($indexerId);
            $idx->reindexAll();
        }
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
     * Initiate product reindex by product ids
     *
     * @param array $productIdsToReindex
     * @return void
     */
    private function reindexProducts($productIdsToReindex = [])
    {
        $indexers = [
            'catalog_category_product',
            'catalog_product_price',
            'catalog_product_flat'
        ];
        foreach ($indexers as $code) {
            $indexer = $this->indexerRegistry->get($code);
            $indexer->reindexAll();
        }
//        foreach ($indexers as $code) {
//            $indexer = $this->indexerRegistry->get($code);
//            if (is_array($productIdsToReindex) && count($productIdsToReindex) > 0 && !$indexer->isScheduled()) {
//                $indexer->reindexList($productIdsToReindex);
//            }
//        }
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'ftp_sage_100_update_product_info';
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
        if (isset($rowData[self::COLUMN_ITEM_CODE]) && isset($rowData[self::COLUMN_STANDARD_UNIT_PRICE]) && isset($rowData[self::COLUMN_TOTAL_QUANTITY_ON_HAND])) {
            if ('' === $rowData[self::COLUMN_ITEM_CODE]) {
                $this->addRowError(ValidatorInterface::ERROR_SKU_IS_EMPTY, $rowNum);
            }
        } else {
            $this->addRowError(ValidatorInterface::ERROR_SKU_IS_EMPTY, $rowNum);
        }
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
}