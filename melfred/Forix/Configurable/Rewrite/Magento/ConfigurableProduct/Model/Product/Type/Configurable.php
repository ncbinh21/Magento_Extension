<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\Configurable\Rewrite\Magento\ConfigurableProduct\Model\Product\Type;


class Configurable extends \Magento\ConfigurableProduct\Model\Product\Type\Configurable
{
    protected $_rigModelId = null;
    protected function _prepareProduct(\Magento\Framework\DataObject $buyRequest, $product, $processMode)
    {
        if($rigModel = $buyRequest->getRigModel()){
            $superAttribute = $buyRequest->getSuperAttribute();
            foreach($rigModel as $key => $value){
                if(empty($value)){
                    continue;
                }
                $this->_rigModelId = $key;
                $superAttribute[$key] = $value;
            }
            $buyRequest->setSuperAttribute($superAttribute);

        }
        return parent::_prepareProduct($buyRequest, $product, $processMode);
    }

    public function getProductByAttributes($attributesInfo, $product)
    {
        if (is_array($attributesInfo) && !empty($attributesInfo)) {
            $productCollection = $this->getUsedProductCollection($product)->addAttributeToSelect('name');
            foreach ($attributesInfo as $attributeId => $attributeValue) {
                if(!is_null($this->_rigModelId) && $attributeId == $this->_rigModelId){
                    $productCollection->addAttributeToFilter(
                        [
                            ['attribute'=>$attributeId, 'finset'=>$attributeValue]
                        ]
                    );
                    continue;
                }
                $productCollection->addAttributeToFilter($attributeId, $attributeValue);
            }
            /** @var \Magento\Catalog\Model\Product $productObject */
            $productObject = $productCollection->getFirstItem();
            $productLinkFieldId = $productObject->getId();
            if ($productLinkFieldId) {
                return $this->productRepository->getById($productLinkFieldId);
            }

            foreach ($productCollection as $productObject) {
                $checkRes = true;
                foreach ($attributesInfo as $attributeId => $attributeValue) {
                    $code = $this->getAttributeById($attributeId, $product)->getAttributeCode();
                    if ($productObject->getData($code) != $attributeValue) {
                        $checkRes = false;
                    }
                }
                if ($checkRes) {
                    return $productObject;
                }
            }
        }
        return null;
    }
}