<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 */
namespace Forix\Catalog\Block\Product\View;

class Price extends \Magento\Catalog\Block\Product\View
{
    /**
     * @return bool
     */
    public function isShowPrice()
    {
        $product = $this->getProduct();
        $isColor = $this->isChangePrice($product);
        if($product->getPriceInfo()->getPrice('regular_price') && $product->getPriceInfo()->getPrice('final_price')) {
            if($product->getPriceInfo()->getPrice('regular_price')->getValue() == $product->getPriceInfo()->getPrice('final_price')->getValue()) {
                if(!$isColor && $product->getTypeId() == 'simple') {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isChangePrice($product)
    {
        $typeCustom = ['drop_down', 'radio', 'multiple'];
        if($options = $product->getOptions()){
            foreach ($options as $option) {
                if (in_array($option->getType(), $typeCustom, true) || $option->getIsColorpicker()) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param $product
     * @return bool
     */
    public function isCustomOption($product)
    {
        if($options = $product->getOptions()){
            foreach ($options as $option) {
                if($option->getIsColorpicker()){
                    return true;
                }
            }
        }
        return false;
    }
}