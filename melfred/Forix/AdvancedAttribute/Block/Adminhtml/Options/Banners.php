<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\AdvancedAttribute\Block\Adminhtml\Options;

/**
 * Adminhtml refunded report page content block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Banners extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'attributes/grid/container.phtml';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Forix_AdvancedAttribute';
        $this->_controller = 'adminhtml_options';
        $this->_headerText = __('Attribute Banners');
        parent::_construct();

        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_back',
            ['label' => __('Back'), 'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()), 'class' => 'back']
        );
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Sync Attribute Options'), 'onclick' => sprintf("location.href = '%s';", $this->getSyncUrl()), 'class' => 'primary']
        );

    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/save', ['_current' => true]);
    }
    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', ['_current' => true]);
    }

    /**
     * Get URL for add button
     *
     * @return string
     */
    public function getSyncUrl()
    {
        return $this->getUrl('*/attributes/sync', ['_current' => true]);
    }
}
