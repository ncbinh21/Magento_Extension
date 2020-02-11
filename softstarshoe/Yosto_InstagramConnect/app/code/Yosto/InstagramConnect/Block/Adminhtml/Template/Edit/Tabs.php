<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Block\Adminhtml\Template\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

/**
 * Class Tabs
 * @package Yosto\InstagramConnect\Block\Adminhtml\Template\Edit
 */
class Tabs extends WidgetTabs
{
    /**
     * Override constructor
     */
    public function _construct()
    {
        $this->setId('template_edit_tabs');
        $this->setDestElementId("edit_form");
        $this->setTitle(__('Template information'));
        parent::_construct();
    }

    /**
     * @return $this
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _beforeToHtml()
    {
        $this->addTab(
            'template_info',
            [
                'label' => __('Template info'),
                'title' => __('Template info'),
                'content' => $this
                    ->getLayout()
                    ->addBlock(
                        'Yosto\InstagramConnect\Block\Adminhtml\Template\Edit\Tab\Info'
                    )->toHtml(),
                'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
