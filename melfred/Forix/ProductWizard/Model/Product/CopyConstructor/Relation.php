<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 12/06/2018
 * Time: 16:17
 */
namespace Forix\ProductWizard\Model\Product\CopyConstructor;

use Forix\ProductWizard\Model\ResourceModel\Product\Link as RelationLink;

class Relation implements \Magento\Catalog\Model\Product\CopyConstructorInterface
{
    /**
     * @param $product
     * @param \Magento\Catalog\Model\Product\Link $link
     * @return mixed
     */
    protected function getRelationLinkCollection($product, $link){
        $collection = $link->getLinkCollection();
        $collection->setProduct($product);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
    /**
     * Build product links
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $duplicate
     * @return void
     */
    public function build(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Product $duplicate)
    {
        $data = [];
        $link = $product->getLinkInstance();
        $link->setLinkTypeId(RelationLink::LINK_TYPE_RELATION);
        $attributes = [];
        foreach ($link->getAttributes() as $attribute) {
            if (isset($attribute['code'])) {
                $attributes[] = $attribute['code'];
            }
        }
        /** @var \Magento\Catalog\Model\Product\Link $link  */
        foreach ($this->getRelationLinkCollection($product, $link) as $link) {
            $data[$link->getLinkedProductId()] = $link->toArray($attributes);
        }
        $duplicate->setRelationProducts($data);
    }
}