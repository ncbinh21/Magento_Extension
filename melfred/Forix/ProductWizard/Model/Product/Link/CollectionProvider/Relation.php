<?php

/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 12/06/2018
 * Time: 11:47
 */

namespace Forix\ProductWizard\Model\Product\Link\CollectionProvider;
use Forix\ProductWizard\Model\ResourceModel\Product\Link as RelationLink;
class Relation implements \Magento\Catalog\Model\ProductLink\CollectionProviderInterface
{
    
    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(\Magento\Catalog\Model\Product $product)
    {
        if (!$product->hasRelationProducts()) {
            $collection = $product->getLinkInstance()->setLinkTypeId(RelationLink::LINK_TYPE_RELATION)->getProductCollection()->setIsStrongMode();
            $collection->setProduct($product);
            $products = [];
            foreach ($collection as $_product) {
                $products[] = $_product;
            }
            $product->setRelationProducts($products);
        }
        return $product->getRelationProducts();
    }
}