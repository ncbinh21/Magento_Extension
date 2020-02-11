<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 21
 * Time: 17:54
 */

namespace Forix\ProductListTemplate\Block\Product\Renderer\Listing;

class Configurable extends \Magento\Swatches\Block\Product\Renderer\Listing\Configurable
{

    /**
     * @return array
     */
    protected function getSwatchAttributesData()
    {
        $swatchAttributeData = parent::getSwatchAttributesData();
        foreach ($swatchAttributeData as &$item) {
            $item['use_product_image_for_swatch'] = true;
        }
        return $swatchAttributeData;
    }
}