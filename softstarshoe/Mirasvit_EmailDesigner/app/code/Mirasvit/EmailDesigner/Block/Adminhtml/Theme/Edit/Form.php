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
 * @package   mirasvit/module-email-designer
 * @version   1.0.16
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailDesigner\Block\Adminhtml\Theme\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\EmailDesigner\Model\Config\Source\TemplateType as SourceTemplateType;

class Form extends WidgetForm
{
    /**
     * @var SourceTemplateType
     */
    protected $sourceTemplateType;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * Form constructor.
     *
     * @param SourceTemplateType $sourceTemplateType
     * @param FormFactory        $formFactory
     * @param Registry           $registry
     * @param Context            $context
     */
    public function __construct(
        SourceTemplateType $sourceTemplateType,
        FormFactory $formFactory,
        Registry $registry,
        Context $context
    ) {
        $this->sourceTemplateType = $sourceTemplateType;
        $this->formFactory = $formFactory;
        $this->registry = $registry;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        $model = $this->getModel();

        $general = $form->addFieldset('general', ['legend' => __('General Information'), 'class' => 'fieldset-wide']);

        if ($model->getId()) {
            $general->addField('design_id', 'hidden', [
                'name'  => 'design_id',
                'value' => $model->getId(),
            ]);
        }

        $general->addField('title', 'text', [
            'label'    => __('Name'),
            'required' => true,
            'name'     => 'title',
            'value'    => $model->getTitle(),
        ]);

        $general->addField('template_type', 'select', [
            'label'    => __('Type'),
            'required' => true,
            'name'     => 'template_type',
            'value'    => $model->getTemplateType(),
            'values'   => $this->sourceTemplateType->toOptionArray(),
        ]);

        $htmlEditor = $this->getLayout()->createBlock('Magento\Backend\Block\Template')
            ->setTemplate('Mirasvit_EmailDesigner::theme/editor.phtml')
            ->setModel($model);

        $general->addField('editor', 'note', [
            'label' => __('Template'),
            'text'  => $htmlEditor->toHtml(),
        ]);

        return parent::_prepareForm();
    }

    /**
     * Theme model
     *
     * @return \Mirasvit\EmailDesigner\Model\Theme;
     */
    protected function getModel()
    {
        return $this->registry->registry('current_model');
    }
}
