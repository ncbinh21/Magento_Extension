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
use \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product\RelationAttributes;

class ConfigurableVariations extends AbstractColumnType
{
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
                    $labels[] = $attribute['frontend_label'];
                    if (strtolower($attribute['frontend_label']) == $attributeLabel) {
                        $result[$attribute['code']] = $attribute['frontend_label'];
                        break;
                    }
                };
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


    protected function getAttributeValueMap($rawData, $attribute_labels)
    {
        $attribute_values = [];
        $attribute_labelMaps = [];
        $attributes = $this->retrieveAttributeFromLabels($rawData[ImportProduct::COL_TYPE], $attribute_labels);
        foreach ($attributes as $attributeCode => $attributeLabel) {
            $columnName = $this->_columnByAttributeCode[$attributeCode];
            if (isset($rawData[$columnName]) && $rawData[$columnName]) {
                if (0 !== strcmp('mb_rig_model', $attributeCode)) {
                    array_push($attribute_labelMaps, $attributeLabel);
                    array_push($attribute_values, trim($rawData[$columnName]));
                }
            }
        }
        return [
            $attribute_values,
            $attribute_labelMaps
        ];
    }

    public function customValue($value, $rawData = [])
    {
        if (isset($result[$this->_productTypeCol])) {
            $rawData[$this->_productTypeCol] = strtolower(trim($rawData[$this->_productTypeCol]));
        }
        if (isset($rawData[$this->_productTypeCol]) && Configurable::TYPE_CODE == $rawData[$this->_productTypeCol]) {
            $result['configurable_variations'] = [];
            $result['configurable_variation_labels'] = [];
            $attribute_labels = array_map('trim', explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, preg_replace('/\s\s+/', ' ', $rawData['relation_attributes'])));
            $previousAttributeLabelMaps = null;
            $countNum = [];
            $max = 0;
            $maxNum = 0;
            /**
             * @var $relation \Magento\Framework\Model\AbstractModel
             */
            foreach ($this->_relationsSource->getRelationCollection() as $relation) {
                if (in_array( $rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]], $relation->getData('sku_parent'))) {
                    list($attribute_values, $attribute_label_map) = $this->getAttributeValueMap($relation->getData('row_data'), $attribute_labels);
                    $count = count($attribute_label_map);
                    $countNum[$count] = (isset($countNum[$count])) ? $countNum[$count] + 1 : 0;
                    if ($max < $countNum[$count]) {
                        $max = $countNum[$count];
                        $maxNum = $count;
                        $previousAttributeLabelMaps = $attribute_label_map;
                    }
                }
            }
            foreach ($this->_relationsSource->getRelationCollection() as $relation) {
                if (in_array( $rawData[$this->_columnByAttributeCode[ImportProduct::COL_SKU]], $relation->getData('sku_parent'))) {

                    list($attribute_values, $attribute_label_map) = $this->getAttributeValueMap($relation->getData('row_data'), $attribute_labels);
                    if (null !== $previousAttributeLabelMaps) {
                        if (count(array_diff($previousAttributeLabelMaps, $attribute_label_map))) {
                            if ($maxNum < count($attribute_label_map)) {
                                $attribute_label_map = $previousAttributeLabelMaps;
                            } else {
                                $this->_addMessages(["Child SKU: " . ($relation->getData('sku'))]);
                                continue;
                            }
                        }
                    }
                    $result['configurable_variation_labels'] = $this->retrieveAttributeFromLabels($rawData[ImportProduct::COL_TYPE], $attribute_label_map);
                    if (count($result['configurable_variation_labels'])) {
                        $variationAttributeCode = array_keys($result['configurable_variation_labels']);
                        $tempAttribute_values = $attribute_values;
                        foreach ($attribute_values as $index => $attribute_value) {
                            if (false !== strpos($attribute_value, '|')) {
                                unset($variationAttributeCode[$index]);
                                unset($tempAttribute_values[$index]);
                            }
                        }
                        try {
                            $_configurable_variations = array_combine($variationAttributeCode, $tempAttribute_values);
                            if (count($_configurable_variations)) {
                                $_configurable_variations = array_merge(['sku' => $relation->getData('sku')], $_configurable_variations);
                                $_configurable_variations = urldecode(http_build_query($_configurable_variations, '', ','));

                                array_push($result['configurable_variations'], $_configurable_variations);
                                //"sku=MH01-XS-Black,size=XS"
                                $new_value = implode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $result['configurable_variations']);
                                if ($new_value) {
                                    $value = $new_value;
                                } else {
                                    $this->_addMessages(["Child SKU: " . ($relation->getData('sku'))]);
                                }
                            } else {
                                $this->_addMessages(["Child SKU: " . ($relation->getData('sku'))]);
                            }
                        } catch (\Exception $e) {
                            //echo "\r\n " . ($relation->getData('sku_parent')) . ":"  . $e->getMessage() ."\r\n";
                            $this->_addMessages(["Child SKU: " . ($relation->getData('sku'))]);
                        }
                    }
                }
            }
        }
        return $value;
    }
}