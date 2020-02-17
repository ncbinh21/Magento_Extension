<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/06/2018
 * Time: 23:21
 */
namespace Forix\ProductWizard\Model\ProductLink\Converter;
class DefaultConverter implements \Magento\Catalog\Model\ProductLink\Converter\ConverterInterface
{

    /**
     * Convert product to array representation
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function convert(\Magento\Catalog\Model\Product $product)
    {
        return [
            'type' => $product->getTypeId(),
            'sku' => $product->getSku(),
            'position' => $product->getPosition(),
            'is_required' => $product->getIsRequired()
        ];
    }
}