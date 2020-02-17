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



namespace Mirasvit\Seo\Model\Template\Rule\Condition;

use Mirasvit\Seo\Model\Config as Config;

class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * @var \Mirasvit\Seo\Model\Template\Rule\Condition\ValidateFactory
     */
    protected $templateRuleConditionValidateFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

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
     * @param \Magento\Rule\Model\Condition\Context                       $context
     * @param \Mirasvit\Seo\Model\Template\Rule\Condition\ValidateFactory $templateRuleConditionValidateFactory
     * @param \Magento\Framework\Registry                                 $registry
     * @param \Magento\Framework\App\RequestInterface                     $request
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Mirasvit\Seo\Model\Template\Rule\Condition\ValidateFactory $templateRuleConditionValidateFactory,
        \Magento\Framework\Registry                                 $registry,
        \Magento\Framework\App\RequestInterface                      $request,
        array $data = []
    ) {
        $this->templateRuleConditionValidateFactory = $templateRuleConditionValidateFactory;
        $this->registry = $registry;
        $this->request = $request;
        parent::__construct($context, $data);
        $this->setType('Mirasvit\Seo\Model\Template\Rule\Condition\Combine');
    }

    /**
     * @return array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getNewChildSelectOptions()
    {
        $productCondition = $this->templateRuleConditionValidateFactory->create();
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
                'value' => 'Mirasvit\Seo\Model\Template\Rule\Condition\Validate|'.$code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, [
            [
                'value' => 'Mirasvit\Seo\Model\Template\Rule\Condition\Combine',
                'label' => __('Conditions Combination'),
            ],
            [
                'label' => __('Categories and Layered navigation'),
                'value' => $attributes['category'],
            ],
        ]);

        $model = $this->registry->registry('current_template_model');
        if (!$model) { // use with Conditions Combination
            if ($this->request->getParam('ruletype')) {
                $ruletype = preg_replace('/\D/', '', $this->request->getParam('ruletype'));
            }
        }
        if (($model && $model->getRuleType()
            && $model->getRuleType() == Config::PRODUCTS_RULE)
            || (isset($ruletype) && $ruletype == Config::PRODUCTS_RULE)) {
            $conditions = array_merge_recursive($conditions, [
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
        }

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
