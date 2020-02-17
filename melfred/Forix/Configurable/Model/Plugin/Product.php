<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/08/2018
 * Time: 12:10
 */

namespace Forix\Configurable\Model\Plugin;

class Product
{

    /**
     * Unset Option image role if product is not simple
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array|string $imageRoles
     * @return array
     */
    public function afterGetMediaAttributes(\Magento\Catalog\Model\Product $product, $imageRoles)
    {
        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            if (is_array($imageRoles)) {
                unset($imageRoles['mb_option_image']);
            }
        }

        return $imageRoles;
    }
}