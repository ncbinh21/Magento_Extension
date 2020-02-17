<?php

namespace Forix\CatalogImport\Model\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\CatalogImportExport\Model\Import\Product\RowValidatorInterface as ValidatorInterface;
use Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor;
use Magento\Framework\Stdlib\DateTime;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingError;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Catalog\Model\Product\Visibility;

class Product extends \Magento\CatalogImportExport\Model\Import\Product
{
    protected $_resourceConnection;
    protected $_storeManager;
    //protected $_selectAttributes;
    protected $_defaultAttributes = array(
        self::COL_ATTR_SET => 'Default',
        self::COL_STORE_VIEW_CODE => 'default',
        self::COL_PRODUCT_WEBSITES => 'base',
        //self::COL_TYPE => 'simple',
        'use_config_manage_stock' => 0,
        'manage_stock' => 0,
        //'use_config_backorders' => 0,
        'qty' => 0,
        //ImportProduct::COL_TYPE => 'simple',
        //'use_config_backorders' => 0,
        //'use_config_manage_stock'   => 0,
        //'manage_stock'  => 0,
        'estimated_delivery_enable' => 'Inherited',
        'estimated_shipping_enable' => 'Inherited',
    );


    protected $_bundleConfigs = array(
        'price_type' => 'dynamic',
        'sku_type' => 'dynamic',
        'price_view' => 'Price range',
        'weight_type' => 'dynamic',
        'shipment_type' => 'Separately',
    );

    CONST CUSTOM_FIELDS_MAP = [];

    const MY_PSEUDO_MULTI_LINE_SEPARATOR = ',';

    protected function doDataFilter($data)
    {
        $this->convertData($data, $this::CUSTOM_FIELDS_MAP);
        return $data;
    }

    /*
     * Modify for data mapping
     * */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $prodAttrColFac,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\ImportExport\Model\Import\Config $importConfig,
        \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory,
        \Magento\CatalogImportExport\Model\Import\Product\OptionFactory $optionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory,
        \Magento\CatalogImportExport\Model\Import\Product\Type\Factory $productTypeFactory,
        \Magento\Catalog\Model\ResourceModel\Product\LinkFactory $linkFactory,
        \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory $proxyProdFactory,
        \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        DateTime $dateTime,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver,
        \Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor,
        \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor $categoryProcessor,
        \Magento\CatalogImportExport\Model\Import\Product\Validator $validator,
        ObjectRelationProcessor $objectRelationProcessor,
        TransactionManagerInterface $transactionManager,
        \Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor $taxClassProcessor,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\Url $productUrl,
        array $data = []
    )
    {
        $this->_entityTypeId = 4;
        parent::__construct(
            $jsonHelper,
            $importExportData,
            $importData,
            $config,
            $resource,
            $resourceHelper,
            $string,
            $errorAggregator,
            $eventManager,
            $stockRegistry,
            $stockConfiguration,
            $stockStateProvider,
            $catalogData,
            $importConfig,
            $resourceFactory,
            $optionFactory,
            $setColFactory,
            $productTypeFactory,
            $linkFactory,
            $proxyProdFactory,
            $uploaderFactory,
            $filesystem,
            $stockResItemFac,
            $localeDate,
            $dateTime,
            $logger,
            $indexerRegistry,
            $storeResolver,
            $skuProcessor,
            $categoryProcessor,
            $validator,
            $objectRelationProcessor,
            $transactionManager,
            $taxClassProcessor,
            $scopeConfig,
            $productUrl,
            $data
        );

        $this->_resourceConnection = $resource;
        $this->_storeManager = $storeManager;
    }

    public function validateData()
    {
        if (!$this->_dataValidated) {
            $this->getErrorAggregator()->clear();
            $this->_saveValidatedBunches();
            $this->_dataValidated = true;
        }
        return $this->getErrorAggregator();
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
                $currentDataSize = strlen(serialize($bunchRows));
                $startNewBunch = false;
                $nextRowBackup = [];
            }
            if ($source->valid()) {
                try {
                    $rowData = array_filter(array_merge($this->_defaultAttributes, $source->current()), array($this, '_myFilter'));
                    if (isset($rowData[self::COL_TYPE]) && $rowData[self::COL_TYPE] == 'bundle') {
                        $rowData = array_merge($rowData, $this->_bundleConfigs);
                    }
                } catch (\InvalidArgumentException $e) {
                    $this->addRowError($e->getMessage(), $this->_processedRowsCount);
                    $this->_processedRowsCount++;
                    $source->next();
                    continue;
                }
                $rowData = $this->doDataFilter($rowData);
                $this->_fieldsMap = array_diff_key($this->_fieldsMap, $this::CUSTOM_FIELDS_MAP);

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


    protected function convertData(&$data, $map)
    {
        foreach ($map as $to => $from) {
            if (isset($data[$from])) {
                $customValue = trim($data[$from]);
                /*$attrParams = $this->retrieveAttributeByCode($to);
                if (false !== $attrParams) {
                    $customValue = $this->customAttributeValueField($to, $attrParams, $customValue, $data);
                }*/
                $data[$to] = $customValue;
            }
        }
        return $data;
    }


    public function getRowScope(array $rowData)
    {
        if (empty($rowData[self::COL_STORE]) || $rowData[self::COL_STORE] == 'default') {
            return self::SCOPE_DEFAULT;
        }
        return self::SCOPE_STORE;
    }

    protected function _saveProductsData()
    {
        $result = parent::_saveProductsData();

        $this->importDependOptionAttribute();
        $this->importDependBundleOptionAttribute();

        return $result;
    }

    protected function importDependOptionAttribute()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                if (empty($rowData['custom_options'])) {
                    continue;
                }

                $savedData = (isset($this->_oldSku[$rowData[self::COL_SKU]]) ? $this->_oldSku[$rowData[self::COL_SKU]] : false);
                $select = $this->_connection->select()
                    ->from(array('option' => $this->_connection->getTableName('catalog_product_option')), [])
                    ->join(array('option_title' => $this->_connection->getTableName('catalog_product_option_title')), 'option.option_id = option_title.option_id', ['otitle' => 'title'])
                    ->join(array('option_value' => $this->_connection->getTableName('catalog_product_option_type_value')), 'option_value.option_id = option_title.option_id', ['ovalue' => 'option_type_id'])
                    ->join(array('option_value_title' => $this->_connection->getTableName('catalog_product_option_type_title')), 'option_value_title.option_type_id = option_value.option_type_id', ['ovtitle' => 'title'])
                    ->where('option.product_id = ?', $savedData['entity_id']);
                $query = $this->_connection->query($select);

                $mapOptions = [];
                while ($row = $query->fetch()) {
                    $mapOptions[$row['otitle']][$row['ovtitle']] = $row['ovalue'];
                }

                $select = $this->_connection->select()
                    ->from($this->_connection->getTableName('forix_import_product_relations'))
                    ->where('sku_parent = ?', $rowData[self::COL_SKU])
                    ->where('relation_type =?', 'custom_option')
                    ->where('dependency IS NOT NULL')
                    ->where('dependency <> ""');
                $query = $this->_connection->query($select);
                $dataBind = [];
                while ($row = $query->fetch()) {
                    $row['dependency'] = explode(self::PSEUDO_MULTI_LINE_SEPARATOR, $row['dependency']);
                    $childrenValueId = $mapOptions[$row['attribute_label']][$row['attribute_value']];
                    foreach ($row['dependency'] as $_depend) {
                        preg_match_all('/<(.*?)>/', $_depend, $parentValues);
                        $parentValues = $parentValues[1];

                        $parentOption = array_shift($parentValues);
                        foreach ($parentValues as $parentValue) {
                            try {
                                array_push($dataBind, [
                                    'value_id' => $mapOptions[$parentOption][$parentValue],
                                    'product_id' => $savedData['entity_id'],
                                    'children_value_id' => $childrenValueId
                                ]);
                            } catch (\Exception $e) {
                                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import.log');
                                $logger = new \Zend\Log\Logger();
                                $logger->addWriter($writer);
                                $logger->info($e->getMessage());
                                echo $e->getMessage();
                            }
                        }


                    }

                }
                if (!empty($dataBind)) {
                    $this->_connection->insertOnDuplicate($this->_connection->getTableName('optionbundle_value_to_value'), $dataBind);
                }

            }
        }
    }

    protected function importDependBundleOptionAttribute()
    {
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNum => $rowData) {
                if (empty($rowData['bundle_values'])) {
                    continue;
                }

                $savedData = (isset($this->_oldSku[$rowData[self::COL_SKU]]) ? $this->_oldSku[$rowData[self::COL_SKU]] : false);
                $select = $this->_connection->select()
                    ->from(array('option' => $this->_connection->getTableName('catalog_product_bundle_option')), [])
                    ->join(array('option_title' => $this->_connection->getTableName('catalog_product_bundle_option_value')), 'option.option_id = option_title.option_id AND option_title.store_id = 0', ['otitle' => 'title'])
                    ->join(array('option_selection' => $this->_connection->getTableName('catalog_product_bundle_selection')), 'option_selection.option_id = option_title.option_id', ['ovalue' => 'selection_id'])
                    ->join(array('product' => $this->_connection->getTableName('catalog_product_entity')), 'option_selection.product_id = product.entity_id', ['ovtitle' => 'sku'])
                    ->where('option.parent_id = ?', $savedData['entity_id']);
                $query = $this->_connection->query($select);

                $mapOptions = [];
                while ($row = $query->fetch()) {
                    $mapOptions[$row['otitle']][$row['ovtitle']] = $row['ovalue'];
                }

                $select = $this->_connection->select()
                    ->from($this->_connection->getTableName('forix_import_product_relations'))
                    ->where('sku_parent = ?', $rowData[self::COL_SKU])
                    ->where('relation_type =?', 'bundle')
                    ->where('dependency IS NOT NULL')
                    ->where('dependency <> ""');
                $query = $this->_connection->query($select);
                $dataBind = [];
                while ($row = $query->fetch()) {

                    $row['dependency'] = explode(self::PSEUDO_MULTI_LINE_SEPARATOR, $row['dependency']);
                    $childrenValueId = $mapOptions[$row['attribute_label']][$row['sku']];
                    foreach ($row['dependency'] as $_depend) {
                        preg_match_all('/<(.*?)>/', $_depend, $parentValues);
                        $parentValues = $parentValues[1];
                        $parentOption = array_shift($parentValues);
                        foreach ($parentValues as $parentValue) {
                            try {
                                array_push($dataBind, [
                                    'selection_id' => $mapOptions[$parentOption][$parentValue],
                                    'product_id' => $savedData['entity_id'],
                                    'children_selection_id' => $childrenValueId
                                ]);
                            } catch (\Exception $e) {
                                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/import.log');
                                $logger = new \Zend\Log\Logger();
                                $logger->addWriter($writer);
                                $logger->info($e->getMessage());
                                echo $e->getMessage() . "\n";
                            }
                        }


                    }

                }

                if (!empty($dataBind)) {
                    $dataBind = array_values($dataBind);

                    $this->_connection->insertOnDuplicate($this->_connection->getTableName('optionbundle_selection_to_selection'), $dataBind);
                }

            }

        }
    }

    protected function _myFilter($var)
    {
        return ($var !== NULL && $var !== '');
    }

}