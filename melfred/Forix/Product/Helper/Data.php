<?php

namespace Forix\Product\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\CatalogInventory\Api\Data\StockItemInterface;

use Magento\Eav\Model\ResourceModel\Entity\Attribute;
class Data extends AbstractHelper
{
    protected $_stockRegistry;
    protected $_eavAttribute;
    protected $_usedProducts;

    const IN_STOCK_CONFIG_CODE = 'in_stock';
    const BACK_ORDER_CONFIG_CODE = 'back_order';


    public function __construct(
        \Magento\CatalogInventory\Model\StockRegistry $stockRegistry,
        Attribute $eavAttribute,
        Context $context)
    {
        parent::__construct($context);
        $this->_stockRegistry = $stockRegistry;
        $this->_eavAttribute = $eavAttribute;
    }


    /**
     * @param $attrCode
     * @return int
     */
    public function getAttributeIdByCode($attrCode)
    {
        return $this->_eavAttribute->getIdByCode(\Magento\Catalog\Model\Product::ENTITY, $attrCode);
    }


    public function getStockConfigMessage($key)
    {
        return $this->scopeConfig->getValue('forix_catalog/stock/' . $key);
    }

    public function getSystemConfig($key)
    {
        return $this->scopeConfig->getValue($key);
    }

    public function getHeavyBadge()
    {
        return $this->getSystemConfig('forix_catalog/general/heavy_badge_label');
    }

    public function getGroundConditionConfig()
    {
        return $this->getSystemConfig('forix_catalog/general/ground_condition_map');
    }

    public function isShowBackOrder($product) {
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE || $product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL) {
            $stockItem = $this->_stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
            if(!$stockItem->getManageStock()) {
                return 'in_stock';
            }
            if ($stockItem->getIsInStock()) {
                if ($this->verifyStock($stockItem) == 'in_stock') {
                    return 'in_stock';
                }
                if ($stockItem->getQty() == 0) {
                    return 'back_order';
                }
            }

        }
        return 'disable';
    }

    /**
     * @param $product
     */
    public function checkInStock($product) {
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE || $product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL) {
            $stockItem = $this->_stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
            if(!$stockItem->getManageStock()) {
                return true;
            }
            if ($stockItem->getIsInStock()) {
                if ($this->verifyStock($stockItem) == 'in_stock') {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    /**
     * @param StockItemInterface $stockItem
     * @return string
     */
    public function verifyStock(StockItemInterface $stockItem)
    {
        if(!$stockItem->getManageStock()) {
            return self::IN_STOCK_CONFIG_CODE;
        }
        if ($stockItem->getQty() === null && $stockItem->getManageStock()) {
            return false;
        }
        if ($stockItem->getBackorders() == StockItemInterface::BACKORDERS_NO
            && $stockItem->getQty() <= $stockItem->getMinQty()
        ) {
            return false;
        }
        if ($stockItem->getBackorders() !== StockItemInterface::BACKORDERS_NO
            && $stockItem->getQty() <= $stockItem->getMinQty()
        ) {
            return self::BACK_ORDER_CONFIG_CODE;
        }
        return self::IN_STOCK_CONFIG_CODE;
    }

    /**
     * @param  $product \Magento\Catalog\Model\Product
     * @return mixed|string
     */
    public function getStockMessage($product)
    {
        if ($product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE || $product->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL) {
            $stockItem = $this->_stockRegistry->getStockItem($product->getId(), $product->getStore()->getWebsiteId());
            if ($stockItem->getIsInStock()) {
                $configCode = $this->verifyStock($stockItem);
                if (false !== $configCode) {
                    return $product->getData('mb_message_' . $configCode) ?: $this->getStockConfigMessage($configCode);
                }
            }
        } else {
            if ($product->isAvailable()) {
                return $product->getData('mb_message_' . self::IN_STOCK_CONFIG_CODE) ?: $this->getStockConfigMessage(self::IN_STOCK_CONFIG_CODE);
            }
        }
        return '';
    }

	public function getBladeCuttingSize($currentProduct)
	{
		/**
		 * @var $configurable \Magento\ConfigurableProduct\Model\Product\Type\Configurable
		 */
		if ($currentProduct->getTypeId() == "configurable") {
			$configurable = $currentProduct->getTypeInstance();
			$ChildProduct = $configurable->getUsedProducts($currentProduct);
			$attr = [];
			if (!empty($ChildProduct)) {
				foreach ($ChildProduct as $_item) {
					$attBe    = $_item->getResource()->getAttribute('mb_blade_cutting_size')->setStoreId(0)->getFrontend()->getValue($_item);
					$attFe  = $_item->getResource()->getAttribute('mb_blade_cutting_size')->setStoreId(1)->getFrontend()->getValue($_item);
					if ($attBe!="" && $attFe!="" && is_numeric($attBe)) {
						$attr[$attBe] = $attFe;
					}
				}
			}
			if (!empty($attr)) {
				ksort($attr);
				reset($attr);
				$first_key = key($attr);
				end($attr);
				$last_key = key($attr);
				if ($first_key == $last_key) {
					return $attr[$first_key];
				}
				return $attr[$first_key]." - ".$attr[$last_key];
			}
		}

		return '';
	}
}