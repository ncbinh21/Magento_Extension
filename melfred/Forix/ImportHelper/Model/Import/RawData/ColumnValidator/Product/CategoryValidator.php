<?php
/**
 * Created by Hidro Le
 * Project: LoveMyBubbles
 * Job Title: Magento Develop
 * Date: 22/09/2017
 * Time: 11:05
 */

namespace Forix\ImportHelper\Model\Import\RawData\ColumnValidator\Product;

class CategoryValidator extends \Forix\ImportHelper\Model\Import\RawData\ColumnValidator\AbstractColumnType
{
    public function customValue($value, $rawData = [])
    {
        $categories = explode(',', $value);
        foreach ($categories as &$category) {
            if($category) {
                if(false !== strpos($category,'Bits & Blades')){
                    $category = 'Ground Condition'. \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor::DELIMITER_CATEGORY . trim($category);
                }else if(false !== strpos($category,'Reamers')){
                    $category = 'Ground Condition'. \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor::DELIMITER_CATEGORY . trim($category);
                }
                if (false === strpos($category, 'Default Category')) {
                    $category = 'Default Category'. \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor::DELIMITER_CATEGORY . trim($category);
                }
            }else{
                $category = 'Default Category';
            }
        }
        return implode(",", $categories);
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
}