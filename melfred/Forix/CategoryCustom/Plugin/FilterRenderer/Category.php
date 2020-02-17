<?php

namespace Forix\CategoryCustom\Plugin\FilterRenderer;

use Amasty\Shopby\Block\Navigation\FilterRenderer\Category as renderCategory;
use Amasty\Shopby\Model\Source\DisplayMode;

class Category extends \Magento\Framework\View\Element\Template
{
    public function afterRender(renderCategory $subject, $data)
    {
        $filterSetting = $subject->getFilterSetting();
        switch (true) {
            case $filterSetting->getDisplayMode() == DisplayMode::MODE_DROPDOWN:
                $template = $subject::TEMPLATE_STORAGE_PATH.'dropdown.phtml';
                break;
            default:
                $template = 'Forix_CategoryCustom::category/render/label.phtml';
                break;
        }
        $subject->setTemplate($template);
        return $subject->toHtml();
    }
}
