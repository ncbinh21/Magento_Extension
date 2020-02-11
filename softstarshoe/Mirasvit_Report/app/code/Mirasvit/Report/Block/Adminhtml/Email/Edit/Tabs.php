<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Report\Block\Adminhtml\Email\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('report_email_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Email Report Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $generalBlock = '\Mirasvit\Report\Block\Adminhtml\Email\Edit\Tab\General';
        $contentBlock = '\Mirasvit\Report\Block\Adminhtml\Email\Edit\Tab\Content';

        $this->addTab('general', [
            'label'   => __('General Information'),
            'content' => $this->getLayout()->createBlock($generalBlock)->toHtml(),
        ]);
        $this->addTab('content', [
            'label'   => __('Content'),
            'content' => $this->getLayout()->createBlock($contentBlock)->toHtml(),
        ]);

        return parent::_beforeToHtml();
    }
}
