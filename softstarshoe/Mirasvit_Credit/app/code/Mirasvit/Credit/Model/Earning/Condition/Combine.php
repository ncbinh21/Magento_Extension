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



namespace Mirasvit\Credit\Model\Earning\Condition;

use Mirasvit\Credit\Model\Earning\Condition as EarningRuleCondition;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Mirasvit\Credit\Model\Earning\Condition\ProductFactory
     */
    protected $earningRuleConditionProductFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\AddressFactory
     */
    protected $ruleConditionAddressFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Rule\Model\Condition\Context
     */
    protected $context;

    public function __construct(
        ProductFactory $earningRuleConditionProductFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\SalesRule\Model\Rule\Condition\AddressFactory $ruleConditionAddressFactory,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->earningRuleConditionProductFactory = $earningRuleConditionProductFactory;
        $this->request = $request;
        $this->ruleConditionAddressFactory = $ruleConditionAddressFactory;
        $this->context = $context;
        $this->eventManager = $eventManager;

        $this->setType('\\Mirasvit\\Credit\\Model\\Earning\\Condition\\Combine');
    }

    /**
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        if ($this->getRule()->getType()) {
            $type = $this->getRule()->getType();
        } else {
            $type = $this->request->getParam('rule_type');
        }

        $itemAttributes = $this->_getProductAttributes();
        $attributes = $this->convertToAttributes($itemAttributes, 'product', 'Product Attributes');


        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => '\\Mirasvit\\Credit\\Model\\Earning\\Condition\\Combine',
                'label' => __('Conditions Combination'),
            ],
        ]);

        foreach ($attributes as $group => $arrAttributes) {
            $conditions = array_merge_recursive($conditions, [
                [
                    'label' => $group,
                    'value' => $arrAttributes,
                ],
            ]);
        }

        return $conditions;
    }

    /**
     * @param array  $itemAttributes
     * @param string $condition
     * @param string $group
     * @return array
     */
    protected function convertToAttributes($itemAttributes, $condition, $group)
    {
        $attributes = [];
        foreach ($itemAttributes as $code => $label) {
            $attributes[$group][] = [
                'value' => '\\Mirasvit\\Credit\\Model\\Earning\\Condition\\' . ucfirst($condition) . '|' . $code,
                'label' => $label,
            ];
        }

        return $attributes;
    }

    /**
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }

    /**
     * @return array
     */
    protected function _getProductAttributes()
    {
        $productCondition = $this->earningRuleConditionProductFactory->create();
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        return $productAttributes;
    }
}
