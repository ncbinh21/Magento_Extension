<?php
namespace Forix\Custom\Rewrite\Magento\ConfigurableProduct\Block\Product\View\Type;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    private $stockConfiguration;

    public function getAllowProducts()
    {
        if (!$this->hasAllowProducts()) {
            $products = [];
            $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();
            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);

            $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();
            $stockItemRepository  = $objectManager->get('\Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface');
            $scopeId = $this->getStockConfiguration()->getDefaultScopeId();
            $isShow = $this->getStockConfiguration()->isShowOutOfStock($scopeId);
            foreach ($allProducts as $product) {
                if($isShow){
                    $products[] = $product;
                }else{
                    $stockStatus = $stockItemRepository->getStockStatus(
                        $product->getId(),
                        $scopeId
                    );
                    $status = $stockStatus->getStockStatus();
                    if ($product->isSaleable() || $skipSaleableCheck) {
                        if($status ){
                            $products[] = $product;
                        }

                    }
                }

            }
            $this->setAllowProducts($products);
        }
        return $this->getData('allow_products');
    }
    private function getStockConfiguration()
    {
        if ($this->stockConfiguration === null) {
            $this->stockConfiguration = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\CatalogInventory\Api\StockConfigurationInterface::class);
        }
        return $this->stockConfiguration;
    }

}