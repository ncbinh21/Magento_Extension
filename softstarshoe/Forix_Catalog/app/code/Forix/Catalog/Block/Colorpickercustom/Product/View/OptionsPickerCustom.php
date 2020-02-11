<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2 - SoftStart Shoes
 * Date: 06/03/2018
 */

namespace Forix\Catalog\Block\Colorpickercustom\Product\View;

use Magento\Catalog\Model\Product\Option;

class OptionsPickerCustom extends \Orange35\Colorpickercustom\Block\Product\View\Options
{
    public function getOptionHtml(Option $option)
    {
        if($option->getType() != 'field' && $option->getType() != 'area') {
            $type = $this->getGroupOfOption($option->getType());
            if ($option->getIsColorpicker()) {
                switch ($option->getType()) {
                    case Option::OPTION_TYPE_MULTIPLE: // break was intentionally omitted
                    case Option::OPTION_TYPE_CHECKBOX:
                        $type = 'swatch';
                        $option->setType(Option::OPTION_TYPE_MULTIPLE);
                        break;
                    case Option::OPTION_TYPE_DROP_DOWN: // break was intentionally omitted
                    case Option::OPTION_TYPE_RADIO:
                        $option->setType(Option::OPTION_TYPE_DROP_DOWN);
                        $type = 'swatch';
                        break;
                }
            }

            $renderer = $this->getChildBlock($type);
            $renderer->setProduct($this->getProduct())->setOption($option);

            return $this->getChildHtml($type, false);
        }
    }
}