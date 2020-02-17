<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 25/07/2018
 * Time: 18:50
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

use Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType;
use \Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
use \Magento\ConfigurableProduct\Model\Product\Type\Configurable;

class RelationAttributes extends AbstractColumnType
{
    const ATTRIBUTE_CODE = 'relation_attributes';
    /**
     * @var \Magento\ImportExport\Model\Import\Config
     */
    protected $_importConfig;
    protected $_columnByAttributeCode;
    protected $_attributeCodeByColumn;
    protected $_productTypeCol;
    protected $_relationsSource;

    public function __construct(
        \Magento\ImportExport\Model\Import\Config $importConfig,
        \Forix\ImportHelper\Model\Import\Source\Relations $relationsSource,
        $productTypeColumn = ImportProduct::COL_TYPE,
        $linkedWith = '')
    {
        $this->_importConfig = $importConfig;
        $this->_relationsSource = $relationsSource;
        parent::__construct($linkedWith);
    }

    /**
     * @param \Forix\ImportHelper\Model\Import\AbstractEntity $context
     * @return AbstractColumnType
     */
    public function init($context)
    {
        parent::init($context);

        $entities = $this->_importConfig->getEntities();
        if (isset($entities[$context->getSourceEntityCode()])) {
            $importClass = $entities[$context->getSourceEntityCode()]['model'];
            if ($importClass) {
                $this->_columnByAttributeCode = $importClass::CUSTOM_FIELDS_MAP;
                $this->_attributeCodeByColumn = array_flip($importClass::CUSTOM_FIELDS_MAP);
                $this->_productTypeCol = isset($this->_columnByAttributeCode[ImportProduct::COL_TYPE]) ? $this->_columnByAttributeCode[ImportProduct::COL_TYPE] : ImportProduct::COL_TYPE;
            }
        }
        return $this;
    }

    /**
     * @param $entityTypeName
     * @param array $attributeLabels
     * @return array
     */
    protected function retrieveAttributeFromLabels($entityTypeName, $attributeLabels)
    {
        /**
         * @var $entityTypeModel \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType
         */

        $entityTypeModel = $this->context->retrieveEntityTypeByName(strtolower($entityTypeName));
        $result = [];
        if ($entityTypeModel instanceof \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType) {
            $attributeLabels = array_map('strtolower', $attributeLabels);
            foreach ($attributeLabels as $attributeLabel) {
                foreach ($entityTypeModel::$commonAttributesCache as $attribute) {
                    if (strtolower($attribute['frontend_label']) == $attributeLabel) {
                        $result[$attribute['code']] = $attribute['frontend_label'];
                        break;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * @param $value
     * @param $rowData
     * @return bool
     */
    public function validate($value, $rowData)
    {
        return true;
    }

    public function customValue($value, $rawData = [])
    {
        if (isset($result[$this->_productTypeCol])) {
            $rawData[$this->_productTypeCol] = strtolower(trim($rawData[$this->_productTypeCol]));
        }
        if (isset($rawData[$this->_productTypeCol]) && \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE == $rawData[$this->_productTypeCol]) {
            $relation = [];
            //$relation['relation_type'] = Configurable::TYPE_CODE;

            /*$attribute_labels = $rawData[self::ATTRIBUTE_CODE];
            $attribute_values = [];
            $attribute_labelMaps = [];
            $attribute_labels = array_map('trim', explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $attribute_labels));
            $attributes = $this->retrieveAttributeFromLabels($rawData[ImportProduct::COL_TYPE], $attribute_labels);
            foreach ($attributes as $attributeCode => $attributeLabel) {
                $columnName = $this->_columnByAttributeCode[$attributeCode];
                if (isset($rawData[$columnName]) && $rawData[$columnName]) {
                    if(0 !== strcmp('mb_rig_model', $attributeCode)) {
                        array_push($attribute_labelMaps, $attributeLabel);
                        array_push($attribute_values, trim($rawData[$columnName]));
                    }
                } else {
                    $this->_addMessages(["Configurable Config Error: {$attributeLabel}"]);
                    return $value;
                }
            }
            $relation['attribute_value'] = implode('|', $attribute_values);
            $relation[self::ATTRIBUTE_CODE] = implode('|', $attribute_labelMaps);*/

            $relation['sku_parent'] = array_map('trim',  explode(',', $rawData['sku_parent']));
            $relation['row_data'] = $rawData;
            $relation['sku'] = $rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]];
            $this->_relationsSource->addItem($relation);
        }
        return $value;
    }
}