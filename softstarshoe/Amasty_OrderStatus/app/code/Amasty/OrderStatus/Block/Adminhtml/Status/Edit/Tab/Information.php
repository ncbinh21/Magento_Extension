<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Block\Adminhtml\Status\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Information extends Generic implements TabInterface
{
    protected $_orderConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        array $data = []
    )
    {
        $this->_orderConfig = $orderConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    public function getTabLabel()
    {
        return __('Status Information');
    }

    public function getTabTitle()
    {
        return __('Status Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_amasty_order_status');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('amostatus_');
        $fieldsetStatusInformation = $form->addFieldset('status_information_fieldset', ['legend' => __('Status Information')]);
        if ($model->getId()) {
            $fieldsetStatusInformation->addField('id', 'hidden', ['name' => 'id']);
        }

        $fieldsetStatusInformation->addField(
            'status',
            'text',
            [
                'name' => 'status',
                'label' => __('Status Name'),
                'title' => __('Status Name'),
                'required' => true,
            ]
        );

        $states = $this->_prepareStatesToMultiselect($this->_orderConfig->getStates());

        $fieldsetStatusInformation->addField('parent_state', 'multiselect', array(
            'name' => 'parent_state[]',
            'label' => __('Order States To Apply Status To'),
            'title' => __('Order States To Apply Status To'),
            'values' => $states,
        ));

        $fieldsetStatusInformation->addField(
            'is_active',
            'select',
            [
                'name' => 'is_active',
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'options' => [0 => __('No'), 1 => __('Yes')]
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _prepareStatesToMultiselect($states)
    {
        foreach ($states as $key => $value) {
            $states[] = ['value' => $key, 'label' => $value->getText()];
            unset($states[$key]);
        }

        return $states;
    }
}
