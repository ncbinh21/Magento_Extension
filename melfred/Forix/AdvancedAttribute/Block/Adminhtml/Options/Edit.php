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
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Forix_AdvancedAttribute';
        $this->_controller = 'adminhtml_options';
        $this->_headerText = __('Advanced Attribute');
        parent::_construct();

//        $this->buttonList->remove('add');
//        $this->addButton(
//            'filter_form_back',
//            ['label' => __('Back'), 'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()), 'class' => 'back']
//        );
//        $label = 'Save';
//        $this->addButton(
//            'filter_form_submit',
//            ['label' => __($label), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
//        );
        $this->buttonList->remove('delete');
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            -100
        );

    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/attributes/manage', ['_current' => true, 'id' => null]);
    }

    /**
     * Getter of url for "Save and Continue" button
     * tab_id will be replaced by desired by JS later
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '']);
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
