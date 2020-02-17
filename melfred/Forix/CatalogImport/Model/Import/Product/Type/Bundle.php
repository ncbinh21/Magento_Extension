<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: ergomart.local
 */
namespace Forix\CatalogImport\Model\Import\Product\Type;
class Bundle extends \Magento\BundleImportExport\Model\Import\Product\Type\Bundle {
    protected function _isAttributeRequiredCheckNeeded($attrCode)
    {
        if($attrCode == 'price'){
            return false;
        }
        return true;
    }
    protected function insertSelections()
    {
        $selectionTable = $this->_resource->getTableName('catalog_product_bundle_selection');
        $selections = [];
        foreach ($this->_cachedOptions as $productId => $options) {
            foreach ($options as $option) {
                $index = 0;
                foreach ($option['selections'] as $selection) {
                    if (isset($selection['position'])) {
                        $index = $selection['position'];
                    }
                    if ($tmpArray = $this->populateSelectionTemplate(
                        $selection,
                        $option['option_id'],
                        $productId,
                        $index
                    )) {
                        $selections[] = $tmpArray;
                        $index++;
                    }
                }
            }
        }
        if (!empty($selections)) {
            
            $this->connection->insertOnDuplicate(
                $selectionTable,
                $selections,
                [
                    'selection_id',
                    'product_id',
                    'position',
                    'is_default',
                    'selection_price_type',
                    'selection_price_value',
                    'selection_qty',
                    'selection_can_change_qty',
                    'selection_altname'
                ]
            );
        }
        return $this;
    }
    protected function populateSelectionTemplate($selection, $optionId, $parentId, $index)
    {
        if (!isset($selection['parent_product_id'])) {
            if (!isset($this->_cachedSkuToProducts[$selection['sku']])) {
                return false;
            }
            $productId = $this->_cachedSkuToProducts[$selection['sku']];
        } else {
            $productId = $selection['product_id'];
        }
        
        $populatedSelection = [
            'selection_id' => null,
            'option_id' => (int)$optionId,
            'parent_product_id' => (int)$parentId,
            'product_id' => (int)$productId,
            'position' => (int)$index,
            'is_default' => (isset($selection['default']) && $selection['default']) ? 1 : 0,
            'selection_price_type' => (isset($selection['price_type']) && $selection['price_type'] == self::VALUE_FIXED)
                ? self::SELECTION_PRICE_TYPE_FIXED : self::SELECTION_PRICE_TYPE_PERCENT,
            'selection_price_value' => (isset($selection['price'])) ? (float)$selection['price'] : 0.0,
            'selection_qty' => (isset($selection['default_qty'])) ? (float)$selection['default_qty'] : 1.0,
            'selection_can_change_qty' => 1,
            'selection_altname' => $selection['selection_altname']
        ];
        if (isset($selection['selection_id'])) {
            $populatedSelection['selection_id'] = $selection['selection_id'];
        }
        return $populatedSelection;
    }
    protected function parseSelections($rowData, $entityId)
    {
        if(!isset($rowData['bundle_values'])){
            return [];
        }
        parent::parseSelections($rowData, $entityId);
    }
}