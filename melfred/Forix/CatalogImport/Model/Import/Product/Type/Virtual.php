<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: ergomart.local
 */
namespace Forix\CatalogImport\Model\Import\Product\Type;
class Virtual extends \Magento\CatalogImportExport\Model\Import\Product\Type\Virtual{
    protected function _isAttributeRequiredCheckNeeded($attrCode)
    {
        if($attrCode == 'price'){
            return false;
        }
        return true;
    }
}