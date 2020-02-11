<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Catalog\Block\Adminhtml\Product\Attribute\Edit;

use Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tabs as MagentoAttributeEditTabs;

class Tabs
{
    /**
     * @param MagentoAttributeEditTabs $subject
     * @return array
     */
    public function beforeToHtml(MagentoAttributeEditTabs $subject)
    {
        $subject->addTabAfter(
            'amasty_shopby',
            [
                'label' => __('Improved Layered Navigation'),
                'title' => __('Improved Layered Navigation'),
                'content' => $subject->getChildHtml('amshopby'),

            ],
            'front'
        );
        return [];
    }
}
