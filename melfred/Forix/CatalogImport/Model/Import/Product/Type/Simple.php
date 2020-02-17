<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: ergomart.local
 */
namespace Forix\CatalogImport\Model\Import\Product\Type;
class Simple extends \Magento\CatalogImportExport\Model\Import\Product\Type\Simple{
    protected function _isAttributeRequiredCheckNeeded($attrCode)
    {
        if($attrCode == 'price'){
            return false;
        }
        return true;
    }
}