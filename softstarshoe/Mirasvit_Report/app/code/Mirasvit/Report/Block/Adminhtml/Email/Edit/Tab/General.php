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


namespace Mirasvit\Report\Block\Adminhtml\Email\Edit\Tab;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\Report\Api\Data\EmailInterface;

class General extends WidgetForm
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @param Context     $context
     * @param Registry    $registry
     * @param FormFactory $formFactory
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory
    ) {
        $this->registry = $registry;
        $this->formFactory = $formFactory;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var EmailInterface $model */
        $model = $this->registry->registry('current_model');

        $form = $this->formFactory->create();

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        if ($model->getId()) {
            $fieldset->addField('email_id', 'hidden', [
                'name'  => 'id',
                'value' => $model->getId(),
            ]);
        }

        $fieldset->addField('title', 'text', [
            'name'     => 'title',
            'label'    => __('Title'),
            'required' => true,
            'value'    => $model->getTitle(),
        ]);

        $fieldset->addField('is_active', 'select', [
            'label'    => __('Status'),
            'name'     => 'is_active',
            'required' => true,
            'options'  => [
                '1' => __('Enabled'),
                '0' => __('Disabled'),
            ],
            'value'    => $model->getIsActive(),
        ]);

        $fieldset->addField('subject', 'text', [
            'name'     => 'subject',
            'label'    => __('Subject'),
            'required' => true,
            'value'    => $model->getSubject(),
        ]);

        $fieldset->addField('recipient', 'text', [
            'name'     => 'recipient',
            'label'    => __('Recipient'),
            'required' => true,
            'note'     => __('Separate e-mails by commas'),
            'value'    => $model->getRecipient(),
        ]);

        $fieldset->addField('schedule', 'text', [
            'label'    => __('Schedule'),
            'required' => true,
            'name'     => 'schedule',
            'class'    => 'schedule',
            'value'    => $model->getSchedule() ? $model->getSchedule() : '59 23 * * *',
        ]);


        $this->setForm($form);

        return parent::_prepareForm();
    }
}
