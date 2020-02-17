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



namespace Mirasvit\Seo\Block\Adminhtml\CanonicalRewrite;

use Magento\Backend\Block\Widget\Form as WidgetForm;
use Mirasvit\Seo\Api\Data\CanonicalRewriteInterface;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset as RendererFieldset;
use \Magento\Rule\Block\Conditions;
use Magento\Framework\Data\FormFactory;
use Magento\Backend\Model\Url as BackendModelUrl;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Context;

class RuleField extends WidgetForm implements TabInterface
{
    /**
     * @var RendererFieldset
     */
    protected $widgetFormRendererFieldset;

    /**
     * @var Conditions
     */
    protected $conditions;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var BackendModelUrl
     */
    protected $backendUrlManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $_nameInLayout = 'conditions_serialized';

    /**
     * @param RendererFieldset $widgetFormRendererFieldset
     * @param Conditions $conditions
     * @param FormFactory $formFactory
     * @param BackendModelUrl $backendUrlManager
     * @param Registry $registry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        RendererFieldset $widgetFormRendererFieldset,
        Conditions $conditions,
        FormFactory $formFactory,
        BackendModelUrl $backendUrlManager,
        Registry $registry,
        Context $context,
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
        $model = $this->registry->registry(CanonicalRewriteInterface::MODEL);

        $form = $this->formFactory->create();
        $formName = CanonicalRewriteInterface::RULE_FORM_NAME;
        $fieldsetName = CanonicalRewriteInterface::RULE_FIELDSET_NAME;

        $form->setHtmlIdPrefix($formName);
        $renderer = $this->widgetFormRendererFieldset
            ->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($this->backendUrlManager
                ->getUrl('*/canonicalRewrite/newConditionHtml/form/'
                    . CanonicalRewriteInterface::RULE_FIELDSET_NAME), ['form_name' => $fieldsetName])
            ->setFieldSetId($fieldsetName);

        // use ruletype with Conditions Combination
        if ($url = $renderer->getData('new_child_url')) {
            $renderer->setData('new_child_url', $url . '?ruleform=' . $formName);
        }

        $fieldset = $form->addFieldset($fieldsetName, [
            'legend' => __(
                'Rule conditions (leave blank to use only Regular expression)'
            ), ])->setRenderer($renderer);

            $model->getConditions()->setFormName($formName);
            $fieldset->addField('conditions', 'text', [
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
            'required' => true,
            'data-form-part' => $formName,
            ])->setRule($model)->setRenderer($this->conditions);

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
