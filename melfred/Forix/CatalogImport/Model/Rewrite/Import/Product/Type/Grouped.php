<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 27/11/2018
 * Time: 19:56
 */

namespace Forix\CatalogImport\Model\Rewrite\Import\Product\Type;

use Magento\CatalogImportExport\Model\Import\Product;
use Magento\ImportExport\Model\Import;

class Grouped extends \Magento\GroupedImportExport\Model\Import\Product\Type\Grouped
{

    /**
     * Product entity identifier field
     *
     * @var string
     */
    private $productEntityIdentifierField;
    /**
     * Save product type specific data.
     *
     * @return \Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function saveData()
    {
        $newSku = $this->_entityModel->getNewSku();
        $oldSku = $this->_entityModel->getOldSku();
        $attributes = $this->links->getAttributes();
        $productData = [];
        while ($bunch = $this->_entityModel->getNextBunch()) {
            $linksData = [
                'product_ids' => [],
                'attr_product_ids' => [],
                'position' => [],
                'qty' => [],
                'relation' => []
            ];
            foreach ($bunch as $rowNum => $rowData) {
                if ($this->_type != $rowData[Product::COL_TYPE]) {
                    continue;
                }
                $associatedSkusQty = isset($rowData['associated_skus']) ? $rowData['associated_skus'] : null;
                if (!$this->_entityModel->isRowAllowedToImport($rowData, $rowNum) || empty($associatedSkusQty)) {
                    continue;
                }
                $associatedSkusAndQtyPairs = explode(Import::DEFAULT_GLOBAL_MULTI_VALUE_SEPARATOR, $associatedSkusQty);
                $position = 0;
                foreach ($associatedSkusAndQtyPairs as $associatedSkuAndQty) {
                    ++$position;
                    $associatedSkuAndQty = explode(self::SKU_QTY_DELIMITER, $associatedSkuAndQty);
                    $associatedSku = isset($associatedSkuAndQty[0]) ? trim($associatedSkuAndQty[0]) : null;
                    $associatedSku = strtolower($associatedSku);
                    if (isset($newSku[$associatedSku])) {
                        $linkedProductId = $newSku[$associatedSku][$this->getProductEntityIdentifierField()];
                    } elseif (isset($oldSku[$associatedSku])) {
                        $linkedProductId = $oldSku[$associatedSku][$this->getProductEntityIdentifierField()];
                    } else {
                        continue;
                    }
                    $scope = $this->_entityModel->getRowScope($rowData);
                    if (Product::SCOPE_DEFAULT == $scope) {
                        $productData = $this->_entityModel->getNewSku($rowData[Product::COL_SKU]);// $newSku[$rowData[Product::COL_SKU]];
                    } else {
                        $colAttrSet = Product::COL_ATTR_SET;
                        $rowData[$colAttrSet] = $productData['attr_set_code'];
                        $rowData[Product::COL_TYPE] = $productData['type_id'];
                    }
                    $productId = $productData[$this->getProductEntityLinkField()];

                    $linksData['product_ids'][$productId] = true;
                    $linksData['relation'][] = ['parent_id' => $productId, 'child_id' => $linkedProductId];
                    $qty = empty($associatedSkuAndQty[1]) ? 0 : trim($associatedSkuAndQty[1]);
                    $linksData['attr_product_ids'][$productId] = true;
                    $linksData['position']["{$productId} {$linkedProductId}"] = [
                        'product_link_attribute_id' => $attributes['position']['id'],
                        'value' => $position
                    ];
                    if ($qty) {
                        $linksData['attr_product_ids'][$productId] = true;
                        $linksData['qty']["{$productId} {$linkedProductId}"] = [
                            'product_link_attribute_id' => $attributes['qty']['id'],
                            'value' => $qty
                        ];
                    }
                }
            }
            $this->links->saveLinksData($linksData);
        }
        return $this;
    }


    /**
     * Get product entity identifier field
     *
     * @return string
     */
    private function getProductEntityIdentifierField()
    {
        if (!$this->productEntityIdentifierField) {
            $this->productEntityIdentifierField = $this->getMetadataPool()
                ->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class)
                ->getIdentifierField();
        }
        return $this->productEntityIdentifierField;
    }
}