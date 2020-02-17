<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 27/11/2018
 * Time: 14:04
 */

namespace Forix\CatalogImport\Model\Rewrite\Import\Product\Type;
use \Magento\ConfigurableImportExport\Model\Import\Product\Type\Configurable as DefaultConfigurable;
use \Magento\CatalogImportExport\Model\Import\Product as ImportProduct;
class Configurable extends DefaultConfigurable
{

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