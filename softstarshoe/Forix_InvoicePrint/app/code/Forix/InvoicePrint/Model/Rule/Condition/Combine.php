<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 27
 * Time: 15:29
 */

namespace Forix\InvoicePrint\Model\Rule\Condition;

use Magento\Rule\Model\Condition\Combine as RuleCombine;

/**
 * Class Combine
 *
 * @package Forix\InvoicePrint\Model\Rule\Condition
 */
class Combine extends RuleCombine
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Forix\InvoicePrint\Model\Rule\Condition\Order\ItemFactory
     */
    protected $_cartTtemFactory;

    /**
     * Combine constructor.
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Forix\InvoicePrint\Model\Rule\Condition\Order\ItemFactory $conditionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Forix\InvoicePrint\Model\Rule\Condition\Order\ItemFactory $conditionFactory,
        array $data = []
    ) {
        $this->_eventManager = $eventManager;
        $this->_cartTtemFactory = $conditionFactory;
        parent::__construct($context, $data);
        $this->setType('Forix\InvoicePrint\Model\Rule\Condition\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $productAttributes = $this->_cartTtemFactory->create()->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($productAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Forix\InvoicePrint\Model\Rule\Condition\Order\Item|' . $code,
                'label' => $label,
            ];
        }
        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => 'Forix\InvoicePrint\Model\Rule\Condition\Combine',
                    'label' => __('Conditions Combination'),
                ],
                ['label' => __('Product Attribute'), 'value' => $attributes]
            ]
        );
        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('salesrule_rule_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }
        return $conditions;
    }

    /**
     * @param array $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            /** @var \Forix\InvoicePrint\Model\Rule\Condition\Order\Item|Combine $condition */
            $condition->collectValidatedAttributes($productCollection);
        }
        return $this;
    }
}
