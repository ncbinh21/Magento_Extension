<?php

namespace Forix\CatalogImport\Model\Import\Product\Type;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;

/**
 * Importing configurable products
 * @package Magento\ConfigurableImportExport\Model\Import\Product\Type
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Configurable extends \Magento\ConfigurableImportExport\Model\Import\Product\Type\Configurable
{
    /**
     * Save product type specific data.
     *
     * @throws \Exception
     * @return \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function saveData()
    {
        $newSku = $this->_entityModel->getNewSku();
        $oldSku = $this->_entityModel->getOldSku();
        $this->_productSuperData = [];
        $this->_productData = null;

        while ($bunch = $this->_entityModel->getNextBunch()) {
            if ($this->_entityModel->getBehavior() == \Magento\ImportExport\Model\Import::BEHAVIOR_APPEND) {
                $this->_loadSkuSuperDataForBunch($bunch);
            }

            $this->_superAttributesData = [
                'attributes' => [],
                'labels' => [],
                'super_link' => [],
                'relation' => [],
            ];

            $this->_simpleIdsToDelete = [];

            $this->_loadSkuSuperAttributeValues($bunch, $newSku, $oldSku);

            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->_entityModel->isRowAllowedToImport($rowData, $rowNum)) {
                    continue;
                }
                $rowSku = strtolower($rowData[ImportProduct::COL_SKU]);
                $this->_productData = isset($newSku[$rowSku])
                    ? $newSku[$rowSku]
                    : $oldSku[$rowSku];

                if ('configurable' == $this->_productData['type_id']) {
                    // Set super attribute for configurable
                    if (!empty($rowData['configurable_attributes'])) {
                        $configurableAttributesCode = explode($this->_entityModel->getMultipleValueSeparator(), $rowData['configurable_attributes']);
                        foreach ($configurableAttributesCode as $attributeCode) {
                            $attributeCode = trim($attributeCode);
                            if (isset($this->_superAttributes[$attributeCode])) {
                                $attributeId = $this->_superAttributes[$attributeCode]['id'];

                                if ($this->_getSuperAttributeId($this->_productData['entity_id'], $attributeId)) {
                                } elseif (isset($this->_superAttributesData['attributes'][$this->_productData['entity_id']][$attributeId])) {
                                    $attributes = $this->_superAttributesData['attributes'];
                                    $productSuperAttrId = $attributes[$this->_productData['entity_id']][$attributeId]['product_super_attribute_id'];
                                    $this->_collectSuperDataLabels(['_super_attribute_code' => $attributeCode], $productSuperAttrId, $this->_productData['entity_id'], []);
                                } else {
                                    $productSuperAttrId = $this->_getNextAttrId();
                                    $this->_collectSuperDataLabels(['_super_attribute_code' => $attributeCode], $productSuperAttrId, $this->_productData['entity_id'], []);
                                }
                            }
                        }
                    }

                } elseif ('simple' == $this->_productData['type_id'] && !empty($rowData['parent_sku'])) {
                    $parentData = isset($newSku[$rowData['parent_sku']])
                        ? $newSku[$rowData['parent_sku']]
                        : (isset($oldSku[$rowData['parent_sku']]) ? $oldSku[$rowData['parent_sku']] : false);

                    if ($parentData && 'configurable' == $parentData['type_id'] && !empty($parentData['entity_id'])) {
                        // Set parent & child for configurable

                        $this->_superAttributesData['super_link'][] = [
                            'product_id' => $this->_productData['entity_id'],
                            'parent_id' => $parentData['entity_id'],
                        ];
                        $this->_superAttributesData['relation'][] = [
                            'parent_id' => $parentData['entity_id'],
                            'child_id' => $this->_productData['entity_id'],
                        ];
                    }
                }
            }

            $this->_insertData();
        }
        return $this;
    }

    /**
     * Parse variations string to inner format.
     *
     * @param array $rowData
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _parseVariations($rowData)
    {
        $additionalRows = [];
        if (!isset($rowData['configurable_variations'])) {
            return $additionalRows;
        }
        $variations = explode(ImportProduct::PSEUDO_MULTI_LINE_SEPARATOR, $rowData['configurable_variations']);
        foreach ($variations as $variation) {
            $attrRe  = '/\,\w*=|\w*=/s';
            $valRe= '/\w*=/s';
            $matches = []; //Attribute code =;
            preg_match_all($valRe, $variation, $matches);
            $values = preg_split($attrRe, $variation,0, PREG_SPLIT_NO_EMPTY);
            $attributePairs = $matches[0];
            foreach($attributePairs as $index => &$attr){
                $attr = $attr.($values[$index]);
            }
            $fieldAndValuePairsText = $attributePairs;
            //$fieldAndValuePairsText = explode($this->_entityModel->getMultipleValueSeparator(), $variation);
            $additionalRow = [];

            $fieldAndValuePairs = [];
            foreach ($fieldAndValuePairsText as $nameAndValue) {
                $nameAndValue = explode(ImportProduct::PAIR_NAME_VALUE_SEPARATOR, $nameAndValue);
                if (!empty($nameAndValue)) {
                    $value = isset($nameAndValue[1]) ? trim($nameAndValue[1]) : '';
                    $fieldName  = trim($nameAndValue[0]);
                    if ($fieldName) {
                        $fieldAndValuePairs[$fieldName] = $value;
                    }
                }
            }

            if (!empty($fieldAndValuePairs['sku'])) {
                $position = 0;
                $additionalRow['_super_products_sku'] = strtolower($fieldAndValuePairs['sku']);
                unset($fieldAndValuePairs['sku']);
                $additionalRow['display'] = isset($fieldAndValuePairs['display']) ? $fieldAndValuePairs['display'] : 1;
                unset($fieldAndValuePairs['display']);
                foreach ($fieldAndValuePairs as $attrCode => $attrValue) {
                    $additionalRow['_super_attribute_code'] = $attrCode;
                    $additionalRow['_super_attribute_option'] = $attrValue;
                    $additionalRow['_super_attribute_position'] = $position;
                    $additionalRows[] = $additionalRow;
                    $additionalRow = [];
                    $position += 1;
                }
            }
        }
        return $additionalRows;
    }
}
