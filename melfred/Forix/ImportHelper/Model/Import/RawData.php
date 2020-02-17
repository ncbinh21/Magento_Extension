<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 5:29 PM
 */

namespace Forix\ImportHelper\Model\Import;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use \Forix\ImportHelper\Model\Import\RawData\ColumnValidatorInterface;
use \Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
class RawData extends \Forix\ImportHelper\Model\Import\AbstractEntity
{
    public static $fileName = '';
    const COMPONENT_ENTITY_RAWDATA = 'import_helper';

    const PSEUDO_MULTI_LINE_SEPARATOR = '|';

    const COLUMN_ERROR_NAME = 'error_list';
    const COLUMN_FILE_NAME = 'file_name';

    protected $_customCols = [];

    protected function retryCustomColumn()
    {
        return array_merge([
            self::COLUMN_ERROR_NAME => '',
            self::COLUMN_FILE_NAME => ''
        ], $this->_customCols);
    }

    public static $commonAttributesCache = [];
    /**
     * @var \Forix\ImportHelper\Model\RawDataFactory
     */
    protected $_entityRowDataModel;

    protected $_validator;

    protected $_entityTypeModels;

    protected $_importConfig;

    protected $_productTypeFactory;

    protected $_fieldsMap = [];


    protected $_entityCode = '';

    protected $_objectManager;

    protected $_attributeColumnMap = \Forix\CatalogImport\Model\Import\Melfredborzall::CUSTOM_FIELDS_MAP;

    protected $_messageTemplates = [
        ColumnValidatorInterface::ERROR_INVALID_SCOPE => 'Invalid value in Scope column',
        ColumnValidatorInterface::ERROR_INVALID_WEBSITE => 'Invalid value in Website column (website does not exist?)',
        ColumnValidatorInterface::ERROR_INVALID_STORE => 'Invalid value in Store column (store doesn\'t exist?)',
        ColumnValidatorInterface::ERROR_INVALID_ATTR_SET => 'Invalid value for Attribute Set column (set doesn\'t exist?)',
        ColumnValidatorInterface::ERROR_INVALID_TYPE => 'Product Type is invalid or not supported',
        ColumnValidatorInterface::ERROR_INVALID_CATEGORY => 'Category does not exist',
        ColumnValidatorInterface::ERROR_VALUE_IS_REQUIRED => 'Please make sure attribute "%s" is not empty.',
        ColumnValidatorInterface::ERROR_TYPE_CHANGED => 'Trying to change type of existing products',
        ColumnValidatorInterface::ERROR_SKU_IS_EMPTY => 'SKU is empty',
        ColumnValidatorInterface::ERROR_NO_DEFAULT_ROW => 'Default values row does not exist',
        ColumnValidatorInterface::ERROR_CHANGE_TYPE => 'Product type change is not allowed',
        ColumnValidatorInterface::ERROR_DUPLICATE_SCOPE => 'Duplicate scope',
        ColumnValidatorInterface::ERROR_DUPLICATE_SKU => 'Duplicate SKU',
        ColumnValidatorInterface::ERROR_EXCEEDED_MAX_LENGTH => 'Attribute %s exceeded max length',
        ColumnValidatorInterface::ERROR_INVALID_ATTRIBUTE_TYPE => 'Value for \'%s\' attribute contains incorrect value',
        ColumnValidatorInterface::ERROR_INVALID_ATTRIBUTE_OPTION => 'Value for \'%s\' attribute contains incorrect value, see acceptable values on settings specified for Admin',
        ColumnValidatorInterface::ERROR_DUPLICATE_UNIQUE_ATTRIBUTE => 'Duplicated unique attribute',
        ColumnValidatorInterface::ERROR_MEDIA_PATH_NOT_ACCESSIBLE => 'Imported resource (image) does not exist in the local media storage',
        ColumnValidatorInterface::ERROR_MEDIA_URL_NOT_ACCESSIBLE => 'Imported resource (image) could not be downloaded from external resource due to timeout or access permissions',
        ColumnValidatorInterface::ERROR_DUPLICATE_URL_KEY => 'Specified URL key already exists',
    ];

    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Forix\ImportHelper\Model\Import\RawData\RowValidator $validator,
        \Forix\ImportHelper\Model\ResourceModel\RawDataEntityFactory $rawDataFactory,
        \Magento\ImportExport\Model\Import\ConfigInterface $importConfig,
        \Magento\CatalogImportExport\Model\Import\Product\Type\Factory $productTypeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        ProcessingErrorAggregatorInterface $errorAggregator,
        $customColumns = [],
        $entityCode = ''
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->errorAggregator = $errorAggregator;
        $this->_importConfig = $importConfig;
        $this->_productTypeFactory = $productTypeFactory;

        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
        $this->_entityRowDataModel = $rawDataFactory;

        $this->_entityCode = $entityCode;
        $this->_objectManager = $objectManager;
        $this->_initTypeModels();
        $this->_initErrorTemplates();
        $this->_validator = $validator;
        $this->_validator->init($this);
        $this->_customCols = array_merge($this->_customCols, $customColumns);
        $this->_attributeColumnMap = array_flip($this->_attributeColumnMap);
    }

    protected function _initErrorTemplates()
    {
        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }
    }

    /**
     * Initialize product type models.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initTypeModels()
    {
        //\Magento\CatalogImportExport\Model\Import\Product $importProduct,
        if ($this->_entityCode) {
            if (isset($this->_importConfig->getEntities()[$this->getSourceEntityCode()])) {
                $importModel = $this->_importConfig->getEntities()[$this->getSourceEntityCode()]['model'];
                $importProduct = $this->_objectManager->create($importModel, ['based_entity_code' => 'catalog_product']);
                $productTypes = $this->_importConfig->getEntityTypes($this->_entityCode);
                foreach ($productTypes as $productTypeName => $productTypeConfig) {
                    $params = [$importProduct, $productTypeName];
                    if (!($model = $this->_productTypeFactory->create($productTypeConfig['model'], ['params' => $params]))
                    ) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('Entity type model \'%1\' is not found', $productTypeConfig['model'])
                        );
                    }
                    if (!$model instanceof \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __(
                                'Entity type model must be an instance of '
                                . 'Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType'
                            )
                        );
                    }
                    if ($model->isSuitable()) {
                        $this->_entityTypeModels[$productTypeName] = $model;
                    }
                    $this->_fieldsMap = array_merge($this->_fieldsMap, $model->getCustomFieldsMapping());
                    $this->_specialAttributes = array_merge($this->_specialAttributes, $model->getParticularAttributes());
                }
                // remove doubles
                $this->_specialAttributes = array_unique($this->_specialAttributes);
            }
        }
        $entityTypes = $this->_importConfig->getEntityTypes($this->getEntityTypeCode());
        foreach ($entityTypes as $typeName => $typeConfig) {
            $this->_entityTypeModels[$typeName] = $this->_objectManager->create($typeConfig['model']);
        }
        return $this;
    }


    /**
     * Initialize data model.
     * @return \Forix\ImportHelper\Model\ResourceModel\RawDataEntity
     */
    protected function _initRowDataModels()
    {
        $object = $this->_entityRowDataModel->create();
        if ($object instanceof \Forix\ImportHelper\Model\ResourceModel\AbstractRawEntity) {
            $object->init($this);
        }
        return $object;
    }

    public function retrieveEntityTypeByName($entityName)
    {
        if (isset($this->_entityTypeModels[$entityName])) {
            return $this->_entityTypeModels[$entityName];
        }
        return null;
    }

    /**
     * Import data rows.
     *
     * @return boolean
     */
    protected function _importData()
    {
        $rowData = [];
        try {
            // TODO: Implement _importData() method.
            while ($bunch = $this->_dataSourceModel->getNextBunch()) {
                try {
                    $model = $this->_initRowDataModels();
                    $model->addColumnHeader(array_merge($this->getSource()->getColNames(), array_keys($this->retryCustomColumn())));
                    foreach ($bunch as $rowNum => $rowData) {
                        $model->addRowData($rowData);
                    }
                    $model->saveData();
                } catch (\Exception $exception) {
                    echo $exception->getMessage() . "\r\n";
                    $this->addRowError($exception->getMessage(), 0);
                }
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
            print_r($rowData);
        }
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
        //Do Validate Row
        foreach ($this->retryCustomColumn() as $key => $defaultValue){
            if(!isset($rowData[$key])){
                $rowData[$key] = $defaultValue;
            }
        }
        $rowData = $this->_validator->isValid($rowData);
        $this->getErrorAggregator()->isRowInvalid($rowNum);
        return $rowData;
    }

    /**
     * EAV entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::COMPONENT_ENTITY_RAWDATA;
    }

    /**
     * Strip inline translations from text
     *
     * @param array|string &$body
     * @return $this
     */
    protected function stripInlineTranslations(&$body)
    {
        if (is_array($body)) {
            foreach ($body as &$part) {
                $this->stripInlineTranslations($part);
            }
        } else {
            if (is_string($body)) {
                $body = utf8_encode($body);
            }
        }
        return $this;
    }

    /**
     * Validate data rows and save bunches to DB.
     *
     * @return $this|void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
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
        $availableRows = [];
        while ($source->valid()) {
            if ($source->valid()) {
                $availableRows[] = $source->current();
            }
            $source->next();
        }
        $count = count($availableRows);
        $i = 0;

        usort($availableRows, function($a, $b){
            $_valA = strtolower($a[ImportProduct::COL_TYPE]) == 'simple'?1:2;
            $_valB = strtolower($b[ImportProduct::COL_TYPE]) == 'simple'?1:2;
            return $_valA <=> $_valB;
        });
        while ($i < $count || $bunchRows) {
            if ($startNewBunch || !($i < $count)) {
                $this->_dataSourceModel->saveBunch($this->getEntityTypeCode(), $this->getBehavior(), $bunchRows);
                $bunchRows = $nextRowBackup;
                $currentDataSize = strlen(serialize($bunchRows));
                $startNewBunch = false;
                $nextRowBackup = [];
            }
            if ($i < $count) {
                try {
                    $rowData = $availableRows[$i];
                } catch (\InvalidArgumentException $e) {
                    $this->addRowError($e->getMessage(), $this->_processedRowsCount);
                    $this->_processedRowsCount++;
                    $i++;
                    continue;
                }

                $rowData = $this->validateRow($rowData, $i);

                $this->_processedRowsCount++;
                // add row to bunch for save
                $rowData = $this->_prepareRowForDb($rowData);

                $rowSize = strlen($this->jsonHelper->jsonEncode($rowData));

                $isBunchSizeExceeded = $bunchSize > 0 && count($bunchRows) >= $bunchSize;

                if ($currentDataSize + $rowSize >= $maxDataSize || $isBunchSizeExceeded) {
                    $startNewBunch = true;
                    $nextRowBackup = [$i => $rowData];
                } else {
                    $bunchRows[$i] = $rowData;
                    $currentDataSize += $rowSize;
                }
                $i++;
            }
        }
        return $this;
    }

    /**
     * Change row data before saving in DB table.
     *
     * @param array $rowData
     * @return array
     */
    protected function _prepareRowForDb(array $rowData)
    {
        $rowData = parent::_prepareRowForDb($rowData);
        $rowData[self::COLUMN_ERROR_NAME] = null;
        $messages = [];
        foreach ($this->_validator->getMessages() as $message) {
            if (isset($this->_messageTemplates[$message])) {
                $messages[] = $this->_messageTemplates[$message];
            } else {
                $messages[] = $message;
            }
        }
        $rowData[self::COLUMN_ERROR_NAME] = implode(',', $messages);
        $rowData[self::COLUMN_FILE_NAME] = self::$fileName;
        return $rowData;
    }

    /**
     * Parse values of multiselect attributes depends on "Fields Enclosure" parameter
     *
     * @param string $values
     * @return array
     */
    public function parseMultiselectValues($values)
    {
        if (empty($this->_parameters[Import::FIELDS_ENCLOSURE])) {
            return explode(self::PSEUDO_MULTI_LINE_SEPARATOR, $values);
        }
        if (preg_match_all('~"((?:[^"]|"")*)"~', $values, $matches)) {
            return $values = array_map(function ($value) {
                return str_replace('""', '"', $value);
            }, $matches[1]);
        }
        return [$values];
    }

    /**
     * @return string
     */
    public function getSourceEntityCode()
    {
        return $this->_entityCode;
    }

    /**
     * @param $columnName
     * @return string
     */
    public function getAttributeCode($columnName)
    {
        if(isset($this->_attributeColumnMap[$columnName])){
            return $this->_attributeColumnMap[$columnName];
        }
        return $columnName;
    }
}