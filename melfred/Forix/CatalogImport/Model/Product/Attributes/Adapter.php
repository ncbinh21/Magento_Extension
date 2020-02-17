<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: ergomart.local
 */
namespace Forix\CatalogImport\Model\Product\Attributes;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
class Adapter extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity {
    const PSEUDO_MULTI_LINE_SEPARATOR = ',';
    const PSEUDO_MULTI_LINE_SEPARATOR_DEFAULT = '|';
    /**
     * If we should check column names
     *
     * @var bool
     */
    protected $needColumnCheck = false;
    protected $groupFactory;
    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;
    protected $_validators = [];

    protected $_connection;
    protected $_resource;
    protected $_selectAttributes;
    protected $_attributeSource;
    protected $_columnByAttributeCode;
    protected $_attributeCodeByColumn;
    protected $_importConfig;
    protected $_escaper;
    protected $_storeManager;
    protected $_swatchesHelper;
    /**
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data $importExportData,
        \Magento\ImportExport\Model\ResourceModel\Import\Data $importData,
        \Magento\Eav\Model\Config $config,
        ResourceConnection $resource,
        \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Stdlib\StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator,
        \Magento\ImportExport\Model\Import\Config $importConfig,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $prodAttrColFac,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Swatches\Helper\Data $swatchesHelper
    )
    {
        //parent::__construct($jsonHelper,$importExportData,$importData,$config,$resource,$resourceHelper,$string,$errorAggregator);
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;
        $this->_swatchesHelper = $swatchesHelper;

        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }

        $this->_dataSourceModel = $importData;
        $this->_resource = $resource;
        $this->_connection = $resource->getConnection();

        $this->_escaper = $escaper;
        $this->_storeManager = $storeManager;
        $this->_importConfig = $importConfig;
        $entities = $this->_importConfig->getEntities();
        $importClass = $entities['melfred_catalog_product']['model'];
        $this->_columnByAttributeCode = $importClass::CUSTOM_FIELDS_MAP;
        $this->_attributeCodeByColumn = array_flip($importClass::CUSTOM_FIELDS_MAP);

        $attributesCollection = $prodAttrColFac->create()/*->addFieldToFilter(
            'main_table.is_user_defined',
            ['eq' => 1])*/
            ->addFieldToFilter(
                'main_table.entity_type_id',
                ['eq' => 4])
            ->addFieldToFilter(
                'main_table.frontend_input',
                ['in' => array('select', 'multiselect', 'boolean')]);

        $this->_selectAttributes = array();
        foreach($attributesCollection as $attribute){
            $attribute->setStoreId(0);
            if(!($attribute->getSource() instanceof \Magento\Eav\Model\Entity\Attribute\Source\Table)){
                $this->_attributeSource[$attribute->getAttributeCode()] = $attribute->getSource();
            }
            $_tmpData = $attribute->getData();
            $_tmpData['is_text_swatch'] = $this->_swatchesHelper->isTextSwatch($attribute);
            $_tmpData['options'] = array();
            $_attributeOptions = $attribute->getSource()->getAllOptions(false);
            foreach ($_attributeOptions as $option) {

                $_tmpData['options'][(string) $option['label']] = $option['value'];
            }
            $this->_selectAttributes[$attribute->getAttributeCode()] = $_tmpData;
            unset($_tmpData);
            unset($_attributeOptions);
        }
        
    }

    /**
     * Entity type code getter.
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'melfred_product_attributes';
    }

    /**
     * Row validation.
     *
     * @param array $rowData
     * @param int $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        return true;
    }

    /**
     * Create Advanced price data from raw data.
     *
     * @throws \Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $this->getBehavior()) {
            $this->replaceEntity();
        } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $this->getBehavior()) {
            $this->saveEntity();
        }
        return true;
    }

    /**
     * Save newsletter subscriber
     *
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    /**
     * Replace newsletter subscriber
     *
     * @return $this
     */
    public function replaceEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    /**
     * Save and replace newsletter subscriber
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                $selectAttributes = array_keys($rowData);
                $selectAttributes = array_intersect_key(array_flip($selectAttributes),array_flip(array_keys($this->_selectAttributes)));

                foreach($selectAttributes as $attrCode => $col){
                    if(is_null($rowData[$attrCode]) || $rowData[$attrCode] === ''){
                        continue;
                    }
                    if($this->_selectAttributes[$attrCode]['frontend_input'] == 'multiselect'){
                        $values = explode(self::PSEUDO_MULTI_LINE_SEPARATOR, $rowData[$attrCode]);
                        $_values = [];
                        foreach ($values as $_value){
                             $_values = array_merge($_values, explode(self::PSEUDO_MULTI_LINE_SEPARATOR_DEFAULT, $_value));
                        }
                        $values = $_values;
                    }else{
                        $values = [$rowData[$attrCode]];
                    }
                    //$values = array_unique(array_map('trim',array_filter($values)));
                    $values = array_unique(array_map('trim',$values));

                    $optionsToAdd = [];
                    foreach ($values as $value) {
                        //$value = $this->_escaper->escapeHtml($value);
                        /*if(in_array($attrCode,['color','bl_size','bl_size_group']) && is_numeric($value)){
                            $value = sprintf("%02s", $value);
                        }*/
                        if(!isset($this->_selectAttributes[$attrCode]['options'][$value])){
                            $optionsToAdd[] = $value;
                        }
                    }
                    if(count($optionsToAdd)>0){
                        $addedOptions = $this->_addAttributeOption(
                            [
                                'attribute_code'    =>  $attrCode,
                                'attribute_id'      =>  $this->_selectAttributes[$attrCode]['attribute_id'],
                                'values'            =>  $optionsToAdd,
                            ]
                        );
                        if(count($addedOptions) > 0){
                            foreach($addedOptions as $label => $value){
                                $this->_selectAttributes[$attrCode]['options'][$label] = $value;
                            }
                        }

                    }
                }
            }
        }
        return $this;
    }
    public function validateData()
    {
        $this->getErrorAggregator()->clear();

        $this->_saveValidatedBunches();
        $this->_dataValidated = true;
        return $this->getErrorAggregator();
    }
    private function _addAttributeOption($option)
    {
        $stores = $this->_storeManager->getStores(true);
        $optionTable = $this->_resource->getTableName('eav_attribute_option');
        $optionValueTable = $this->_resource->getTableName('eav_attribute_option_value');
        $optionSwatchTable = $this->_resource->getTableName('eav_attribute_option_swatch');
        $result = array();
        if (isset($option['values'])) {
            foreach ($option['values'] as $label) {
                // add option
                $data = ['attribute_id' => $option['attribute_id'], 'sort_order' => 0];
                $this->_connection->insert($optionTable, $data);
                $intOptionId = $this->_connection->lastInsertId($optionTable);
                foreach($stores as $_store){
                    $data = ['option_id' => $intOptionId, 'store_id' => $_store->getId(), 'value' => $label];
                    $this->_connection->insert($optionValueTable, $data);
                }
                if($this->_selectAttributes[$option['attribute_code']]['is_text_swatch']){
                    $data = ['option_id' => $intOptionId, 'store_id' => 0, 'type' => 0, 'value' => $label];
                    $this->_connection->insert($optionSwatchTable, $data);
                }
                $result[$label] = $intOptionId;
            }
        }
        return $result;
    }
}