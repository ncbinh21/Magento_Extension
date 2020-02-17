<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/06/2018
 * Time: 13:52
 */

namespace Forix\ProductWizard\Model;

use Magento\Catalog\Model\Product as DefaultProduct;

use \Forix\ProductWizard\Model\ResourceModel\Product\Link;

class Product extends DefaultProduct
{
    
    public function getRelationLinkCollection()
    {
        $collection = $this->getLinkInstance()->setLinkTypeId(Link::LINK_TYPE_RELATION)->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

    public function getRelationProducts()
    {
        if (!$this->hasRelationProducts()) {
            $products = [];
            $collection = $this->getRelationProductCollection();
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setRelationProducts($products);
        }
        return $this->getData('relation_products');
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getRelationProductCollection()
    {
        $collection = $this->getLinkInstance()->setLinkTypeId(Link::LINK_TYPE_RELATION)->getProductCollection()->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    public function getRelationProductIds()
    {
        if (!$this->hasRelationProductIds()) {
            $ids = [];
            foreach ($this->getRelationProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setRelationProductIds($ids);
        }
        return $this->getData('relation_product_ids');
    }
}