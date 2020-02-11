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



namespace Mirasvit\EmailDesigner\Block\Adminhtml\Template\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form as WidgetForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Mirasvit\EmailDesigner\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;

class Form extends WidgetForm
{
    /**
     * @var ThemeCollectionFactory
     */
    protected $themeCollectionFactory;

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
     * @param ThemeCollectionFactory $themeCollectionFactory
     * @param FormFactory            $formFactory
     * @param Registry               $registry
     * @param Context                $context
     */
    public function __construct(
        ThemeCollectionFactory $themeCollectionFactory,
        FormFactory $formFactory,
        Registry $registry,
        Context $context
    ) {
        $this->themeCollectionFactory = $themeCollectionFactory;
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

        $model = $this->registry->registry('current_model');

        $general = $form->addFieldset('general', ['legend' => __('General Information'), 'class' => 'fieldset-wide']);

        if ($model->getId()) {
            $general->addField('template_id', 'hidden', [
                'name'  => 'template_id',
                'value' => $model->getId(),
            ]);
        }

        $general->addField('title', 'text', [
            'label'    => __('Name'),
            'required' => true,
            'name'     => 'title',
            'value'    => $model->getTitle(),
        ]);

        $general->addField('theme_id', 'select', [
            'label'    => __('Theme'),
            'required' => false,
            'name'     => 'theme_id',
            'value'    => $model->getThemeId(),
            'values'   => $this->themeCollectionFactory->create()->setOrder('title', 'asc')->toOptionArray(),
        ]);

        $general->addField('template_subject', 'text', [
            'label'    => __('Subject'),
            'required' => true,
            'name'     => 'template_subject',
            'value'    => $model->getTemplateSubject(),
        ]);

        if ($this->registry->registry('current_model')->getId() > 0) {
            $htmlEditor = $this->getLayout()->createBlock('\Magento\Backend\Block\Template')
                ->setTemplate('Mirasvit_EmailDesigner::template/editor.phtml')
                ->setModel($model);

            $general->addField('editor', 'note', [
                'text' => $htmlEditor->toHtml()
            ]);
        }

        return parent::_prepareForm();
    }
}
