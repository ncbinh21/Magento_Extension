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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\CanonicalRewrite\Rule\Condition;

use Magento\Rule\Model\Condition\Context;
use Mirasvit\Seo\Model\CanonicalRewrite\Rule\Condition\ValidateFactory;
use Magento\Framework\Registry;
use Magento\Framework\App\RequestInterface;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var array
     */
    protected $groups = [
        'category' => [
            'category_ids',
        ],
        'base' => [
            'name',
            'attribute_set_id',
            'sku',
            'url_key',
            'visibility',
            'status',
            'default_category_id',
            'meta_description',
            'meta_keyword',
            'meta_title',
            'price',
            'special_price',
            'special_price_from_date',
            'special_price_to_date',
            'tax_class_id',
            'short_description',
            'full_description',
        ],
        'extra' => [
            'qty',
            // 'created_at',
            // 'updated_at',
            // 'price_diff',
            // 'percent_discount',
        ],
    ];

    /**
     * @param Context $context
     * @param ValidateFactory $ruleConditionValidateFactory
     * @param Registry $registry
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Mirasvit\Seo\Model\CanonicalRewrite\Rule\Condition\ValidateFactory $ruleConditionValidateFactory,
        \Magento\Framework\Registry                                 $registry,
        \Magento\Framework\App\RequestInterface                      $request,
        array $data = []
    ) {
        $this->ruleConditionValidateFactory = $ruleConditionValidateFactory;
        $this->registry = $registry;
        $this->request = $request;
        parent::__construct($context, $data);
        $this->setType('Mirasvit\Seo\Model\CanonicalRewrite\Rule\Condition\Combine');
    }

    /**
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getNewChildSelectOptions()
    {
        $productCondition = $this->ruleConditionValidateFactory->create();
        $productAttributes = $productCondition->loadAttributeOptions()->getAttributeOption();

        $attributes = [];
        foreach ($productAttributes as $code => $label) {
            $group = 'attributes';
            foreach ($this->groups as $key => $values) {
                if (in_array($code, $values)) {
                    $group = $key;
                }
            }
            $attributes[$group][] = [
                'value' => 'Mirasvit\Seo\Model\CanonicalRewrite\Rule\Condition\Validate|'.$code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => 'Mirasvit\Seo\Model\CanonicalRewrite\Rule\Condition\Combine',
                'label' => __('Conditions Combination'),
            ],
            [
                'label' => __('Categories and Layered navigation'),
                'value' => $attributes['category'],
            ],
            [
                'label' => __('Products'),
                'value' => $attributes['base'],
            ],
            [
                'label' => __('Products Attributes'),
                'value' => $attributes['attributes'],
            ],
            [
                'label' => __('Products Additional'),
                'value' => $attributes['extra'],
            ],
        ]);

        return $conditions;
    }

    /**
     * @param string $productCollection
     *
     * @return $this
     */
    public function collectValidatedAttributes($productCollection)
    {
        foreach ($this->getConditions() as $condition) {
            $condition->collectValidatedAttributes($productCollection);
        }

        return $this;
    }
}
