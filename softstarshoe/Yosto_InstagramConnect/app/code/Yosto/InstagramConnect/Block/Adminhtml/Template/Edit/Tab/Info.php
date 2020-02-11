<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Block\Adminhtml\Template\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Yosto\InstagramConnect\Helper\Constant;
use Yosto\InstagramConnect\Model\Template\Source\Status;

/**
 * Class Info
 * @package Yosto\InstagramConnect\Block\Adminhtml\Template\Edit\Tab
 */
class Info extends Generic implements TabInterface
{
    /**
     * @var Status
     */
    protected $_templateStatus;

    /**
     * Info constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Status $groupStatus
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $templateStatus,
        array $data = [])
    {
        $this->_templateStatus = $templateStatus;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('instagramconnect_template');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('template_');
        $form->setFieldNameSuffix('template');
        $fieldset = $form->addFieldset(
            'template_fieldset',
            ['legend' => __('General')]
        );
        if ($model->getTemplateId()) {
            $fieldset->addField(
                Constant::INSTAGRAM_TEMPLATE_ID,
                'hidden',
                [
                    'name' => Constant::INSTAGRAM_TEMPLATE_ID
                ]
            );
        }
        $fieldset->addField(
            Constant::INSTAGRAM_TEMPLATE_TABLE_TITLE,
            'text',
            [
                'name' => Constant::INSTAGRAM_TEMPLATE_TABLE_TITLE,
                'label' => __('Title'),
                'class' => 'validate-length maximum-length-255',
                'required' => true
            ]
        );
        $fieldset->addField(
            Constant::INSTAGRAM_TEMPLATE_TABLE_DESCRIPTION,
            'text',
            [
                'name' => Constant::INSTAGRAM_TEMPLATE_TABLE_DESCRIPTION,
                'class' => 'validate-length maximum-length-255',
                'label' => __('Description')
            ]
        );
        $fieldset->addField(
            Constant::INSTAGRAM_TEMPLATE_TABLE_STATUS,
            'select',
            [
                'name' => Constant::INSTAGRAM_TEMPLATE_TABLE_STATUS,
                'label' => __('Status'),
                'options' => $this->_templateStatus->toArray()
            ]
        );
        $fieldset->addField(
            Constant::INSTAGRAM_TEMPLATE_TABLE_PC_CODE,
            'hidden',
            [
                'name' => Constant::INSTAGRAM_TEMPLATE_TABLE_PC_CODE
            ]
        );
        $fieldset->addField(
            Constant::INSTAGRAM_TEMPLATE_TABLE_TABLET_CODE,
            'hidden',
            [
                'name' => Constant::INSTAGRAM_TEMPLATE_TABLE_TABLET_CODE
            ]
        );
        $fieldset->addField(
            Constant::INSTAGRAM_TEMPLATE_TABLE_MOBILE_CODE,
            'hidden',
            [
                'name' => Constant::INSTAGRAM_TEMPLATE_TABLE_MOBILE_CODE
            ]
        );
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getTabTitle()
    {
        return __('Template Info');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return mixed
     */
    public function getTabLabel()
    {
        return __('Template Info');
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

}