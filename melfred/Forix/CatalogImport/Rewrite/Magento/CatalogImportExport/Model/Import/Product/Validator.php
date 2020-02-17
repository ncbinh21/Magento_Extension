<?php
/**
 * Created by PhpStorm.
 * User: Hidro Le
 * Date: 8/11/16
 * Time: 6:40 PM
 */

namespace Forix\CatalogImport\Rewrite\Magento\CatalogImportExport\Model\Import\Product;

use Magento\CatalogImportExport\Model\Import\Product as ImportProduct;

class Validator extends \Magento\CatalogImportExport\Model\Import\Product\Validator
{
    /**
     * @param string $attrCode
     * @param array $attrParams
     * @param array $rowData
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function isAttributeValid($attrCode, array $attrParams, array $rowData)
    {
        if ($attrCode == ImportProduct::MEDIA_GALLERY_ATTRIBUTE_CODE || $attrCode == ImportProduct::COL_MEDIA_IMAGE || 'gallery' == $attrCode) return true;
        return parent::isAttributeValid($attrCode, $attrParams, $rowData);
    }
}