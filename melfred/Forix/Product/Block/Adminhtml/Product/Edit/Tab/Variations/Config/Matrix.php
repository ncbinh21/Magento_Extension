<?php
/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 9/12/17
 * Time: 4:08 PM
 */

namespace Forix\Product\Block\Adminhtml\Product\Edit\Tab\Variations\Config;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;

class Matrix extends \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config\Matrix
{
    protected $_matrixHelper;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context, 
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, 
        \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix $variationMatrix,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Image $image,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency, 
        LocatorInterface $locator,
        \Forix\Product\Helper\Product\Matrix $matrixHelper,
        array $data = []
    ){
        $this->_matrixHelper = $matrixHelper;
        parent::__construct($context, $configurableType, $stockRegistry, $variationMatrix, $productRepository, $image, $localeCurrency, $locator, $data);
    }

    /**
     * @return array|null
     */
    public function getProductMatrix(){
        $productMatrix = parent::getProductMatrix();
        foreach ($productMatrix as &$matrix){
            $matrix['recommend_sku'] = $this->_matrixHelper->getRecommendSku($this->getProduct()->getId(), $matrix['productId']);
        }
        return $productMatrix;
    }
}