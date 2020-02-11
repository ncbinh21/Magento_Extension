<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 * Date: 06/03/2018
 */

namespace Forix\Catalog\Block\Product\View;

class OptionsCustom extends \Magento\Catalog\Block\Product\View\Options
{
    public function getOptionHtml(\Magento\Catalog\Model\Product\Option $option)
    {
        if($option->getType() == 'field' || $option->getType() == 'area') {
            $type = $this->getGroupOfOption($option->getType());
            $renderer = $this->getChildBlock($type);

            $renderer->setProduct($this->getProduct())->setOption($option);

            return $this->getChildHtml($type, false);
        }
    }
}