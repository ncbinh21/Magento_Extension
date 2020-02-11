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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Block\Adminhtml\Earning\Edit\Tab;

class Product extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $widgetFormRendererFieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    protected $formFactory;

    /**
     * @var \Magento\Backend\Model\Url
     */
    protected $backendUrlManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    public function __construct(
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Backend\Model\Url $backendUrlManager,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->widgetFormRendererFieldset = $widgetFormRendererFieldset;
        $this->conditions = $conditions;
        $this->formFactory = $formFactory;
        $this->backendUrlManager = $backendUrlManager;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        /** @var \Mirasvit\Credit\Model\Earning $model */
        $model = $this->registry->registry('current_earning');

        $fieldset = $form->addFieldset('rule_conditions_fieldset', ['legend' => __('Conditions')]);

        $renderer = $this->widgetFormRendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($this->backendUrlManager->getUrl(
                '*/earning/newConditionHtml/form/rule_conditions_fieldset')
            );
        $fieldset->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', [
            'name'     => 'conditions',
            'label'    => __('Filters'),
            'title'    => __('Filters'),
            'required' => true,
        ])->setRule($model)
            ->setRenderer($this->conditions);

        $fieldset = $form->addFieldset('action_fieldset', ['legend' => __('Actions')]);

        $fieldset->addField('earning_type', 'select', [
            'label'    => __('Earning Type'),
            'required' => true,
            'name'     => 'earning_type',
            'value'    => $model->getEarningType(),
            'values'   => [
                'fixed'   => 'Fixed amount',
                'percent' => 'Percent of item amount in order',
            ],
        ]);
        $fieldset->addField('earning_amount', 'text', [
            'label'    => __('Earning Amount'),
            'required' => true,
            'name'     => 'earning_amount',
            'value'    => $model->getEarningAmount(),
        ]);


        return parent::_prepareForm();
    }
}
