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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Block\Adminhtml\Template\Edit\Tab;

use Mirasvit\Seo\Model\Config as Config;

class Rule extends \Magento\Backend\Block\Widget\Form implements \Magento\Backend\Block\Widget\Tab\TabInterface
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

    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_serialized';

    /**
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $widgetFormRendererFieldset
     * @param \Magento\Rule\Block\Conditions                       $conditions
     * @param \Magento\Framework\Data\FormFactory                  $formFactory
     * @param \Magento\Backend\Model\Url                           $backendUrlManager
     * @param \Magento\Framework\Registry                          $registry
     * @param \Magento\Backend\Block\Widget\Context                $context
     * @param array                                                $data
     */
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
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->registry->registry('current_template_model');

        $form = $this->formFactory->create();
        $type = $model->getRuleType();
        switch ($type) {
            case Config::PRODUCTS_RULE:
                $formName = 'seo_template_product_form';
                break;

            case Config::CATEGORIES_RULE:
                $formName = 'seo_template_category_form';
                break;

            case Config::RESULTS_LAYERED_NAVIGATION_RULE:
                $formName = 'seo_template_layered_navigation_form';
                break;

            default:
                $formName = 'rule_';
                break;
        }
        $form->setHtmlIdPrefix($formName);
        $fieldsetName = 'rule_conditions_fieldset';

        $renderer = $this->widgetFormRendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($this->backendUrlManager
                ->getUrl('*/template/newConditionHtml/form/rule_conditions_fieldset'), ['form_name' => $fieldsetName])
                ->setFieldSetId($fieldsetName);

        // use ruletype with Conditions Combination
        if (($url = $renderer->getData('new_child_url'))
            && ($ruletype = $model->getRuleType())) {
                $renderer->setData('new_child_url', $url . '?ruletype=' . $ruletype . '&ruleform=' . $formName);
        }

        $fieldset = $form->addFieldset($fieldsetName, [
            'legend' => __(
                'Conditions (leave blank for all elements, depending from rule type)'
            ), ])->setRenderer($renderer);

            $model->getConditions()->setFormName($formName);
            $fieldset->addField('conditions', 'text', [
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
            'required' => true,
            'data-form-part' => $formName,
            ])->setRule($model->getRule())->setRenderer($this->conditions);

            $form->setValues($model->getData());
            $this->setConditionFormName($model->getConditions(), $formName);
            $this->setForm($form);


            return parent::_prepareForm();
    }

    /**
     * Handles addition of form name to condition and its conditions.
     *
     * @param \Magento\Rule\Model\Condition\AbstractCondition $conditions
     * @param string $formName
     * @return void
     */
    private function setConditionFormName(\Magento\Rule\Model\Condition\AbstractCondition $conditions, $formName)
    {
        $conditions->setFormName($formName);
        if ($conditions->getConditions() && is_array($conditions->getConditions())) {
            foreach ($conditions->getConditions() as $condition) {
                $this->setConditionFormName($condition, $formName);
            }
        }
    }
}
