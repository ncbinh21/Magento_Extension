<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/07/2018
 * Time: 17:04
 */
namespace Forix\CatalogImport\Model\Import\Product;
use Magento\CatalogImportExport\Model\Import\Product;
class Option extends \Magento\CatalogImportExport\Model\Import\Product\Option {
    protected function _parseCustomOptions($rowData)
    {
        //$beforeOptionValueSkuDelimiter = ';';
        if (empty($rowData['custom_options'])) {
            return $rowData;
        }
        //$rowData['custom_options'] = str_replace($beforeOptionValueSkuDelimiter, $this->_productEntity->getMultipleValueSeparator(), $rowData['custom_options']);
        $options = [];
        $optionValues = explode(Product::PSEUDO_MULTI_LINE_SEPARATOR, $rowData['custom_options']);
        $k = 0;
        $name = '';
        foreach ($optionValues as $optionValue) {
            $optionValueParams = explode($this->_productEntity->getMultipleValueSeparator(), $optionValue);
            foreach ($optionValueParams as $nameAndValue) {
                $nameAndValue = explode('=', $nameAndValue);
                if (!empty($nameAndValue)) {
                    $value = isset($nameAndValue[1]) ? $nameAndValue[1] : '';
                    $value = trim($value);
                    $fieldName  = trim($nameAndValue[0]);
                    if ($value && ($fieldName == 'name')) {
                        if ($name != $value) {
                            $name = $value;
                            $k = 0;
                        }
                    }
                    if ($name) {
                        $options[$name][$k][$fieldName] = $value;
                    }
                }
            }
            $options[$name][$k]['_custom_option_store'] = $rowData[Product::COL_STORE_VIEW_CODE];
            $k++;
        }
        $rowData['custom_options'] = $options;
        return $rowData;
    }

    /*-----For Blauer Only: add Blauer Type--------*/

    protected function _getMultiRowFormat($rowData)
    {
        $result = parent::_getMultiRowFormat($rowData);
        if(!empty($result)){
            $rowData = $this->_parseCustomOptions($rowData);
            $i = 0;
            foreach ($rowData['custom_options'] as $name => $customOption) {
                foreach ($customOption as $rowOrder => $optionRow) {
                    //if(!empty($optionRow['blauer_type'])){
                        $result[$i]['_custom_option_blauer_type'] = empty($optionRow['blauer_type'])?null:$optionRow['blauer_type'];
                    //}
                    $i++;
                }
            }
        }
        return $result;
    }
    protected function _getOptionData(array $rowData, $productId, $optionId, $type)
    {
        $optionData = parent::_getOptionData($rowData, $productId, $optionId, $type);

        //if(!empty($rowData['_custom_option_blauer_type'])){
            $optionData['blauer_type'] = empty($rowData['_custom_option_blauer_type'])?null:$rowData['_custom_option_blauer_type'];
        //}
        return $optionData;
    }
}