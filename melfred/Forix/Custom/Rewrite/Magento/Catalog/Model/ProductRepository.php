<?php
/**
 * Created by PhpStorm.
 * User: nghia
 * Date: 27/03/2019
 * Time: 16:11
 */

namespace Forix\Custom\Rewrite\Magento\Catalog\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class ProductRepository extends \Magento\Catalog\Model\ProductRepository
{
    private $cacheLimit = 1000;
    /**
     * {@inheritdoc}
     */
    public function get($sku, $editMode = false, $storeId = null, $forceReload = false)
    {
        $cacheKey = $this->getCacheKey([$editMode, $storeId]);
        if (!isset($this->instances[$sku][$cacheKey]) || $forceReload) {
            $product = $this->productFactory->create();

            $productId = $this->resourceModel->getIdBySku($sku);
            if (!$productId) {
                throw new NoSuchEntityException(__('Requested product doesn\'t exist'));
            }
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            if ($storeId !== null) {
                $product->setData('store_id', $storeId);
            }
            $product->load($productId);
            $this->cacheProduct($cacheKey, $product, $product->getId(), $sku);
        }
        if (!isset($this->instances[$sku])) {
            $sku = trim($sku);
        }
        return $this->instances[$sku][$cacheKey];
    }

    /**
     * {@inheritdoc}
     */
    public function getById($productId, $editMode = false, $storeId = null, $forceReload = false)
    {
        $cacheKey = $this->getCacheKey([$editMode, $storeId]);
        if (!isset($this->instancesById[$productId][$cacheKey]) || $forceReload) {
            $product = $this->productFactory->create();
            if ($editMode) {
                $product->setData('_edit_mode', true);
            }
            if ($storeId !== null) {
                $product->setData('store_id', $storeId);
            }
            $product->load($productId);
            if (!$product->getId()) {
                throw new NoSuchEntityException(__('Requested product doesn\'t exist'));
            }
            $this->cacheProduct($cacheKey, $product, $productId, $product->getSku());
        }
        return $this->instancesById[$productId][$cacheKey];
    }

    /**
     * Add product to internal cache and truncate cache if it has more than cacheLimit elements.
     *
     * @param string $cacheKey
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param string|int $productId
     * @param string $productSku
     * @return void
     * @internal param null|string $sku
     */
    private function cacheProduct(
        $cacheKey,
        \Magento\Catalog\Api\Data\ProductInterface $product,
        $productId = null,
        string $productSku = null
    )
    {
        if(is_null($productId)){
            $productId = $product->getId();
        }
        if(is_null($productSku)){
            $productSku = $product->getSku();
        }
        $this->instancesById[$productId][$cacheKey] = $product;
        $this->instances[$productSku][$cacheKey] = $product;

        if ($this->cacheLimit && count($this->instances) > $this->cacheLimit) {
            $offset = round($this->cacheLimit / -2);
            $this->instancesById = array_slice($this->instancesById, $offset, null, true);
            $this->instances = array_slice($this->instances, $offset, null, true);
        }
    }


}