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

class Content extends WidgetForm
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
        $form = $this->formFactory->create();

        $form->setHtmlIdPrefix('blocks_');

        $model = $this->registry->registry('current_model');

        $fieldset = $form->addFieldset(
            'reports_fieldset',
            ['legend' => __('Reports'), 'class' => 'fieldset-wide']
        );

        $fieldset->setRenderer(
            $this->getLayout()->createBlock(
                'Mirasvit\Report\Block\Adminhtml\Email\Edit\Renderer\Blocks'
            )->setEmail($model)
        );


        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
