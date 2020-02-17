<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Admin tax rule content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace  Forix\AdvancedAttribute\Block\Adminhtml;

class AttributeBanners extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'attribute_banners_attributes';
        $this->_blockGroup = 'Forix_AdvancedAttribute';
        $this->buttonList->remove('add');
    }
}
