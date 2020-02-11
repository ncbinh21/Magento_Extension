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


namespace Mirasvit\Credit\Ui\DataProvider\Product\Form\Modifier;

use Mirasvit\Credit\Model\ProductOptionCreditFactory;
use Mirasvit\Credit\Model\Product\Type as CreditType;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Catalog\Model\Product\Type;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Composite extends AbstractModifier
{
    const DATA_SCOPE         = 'data.product';
    const DATA_CREDIT_SCOPE  = 'credit';
    const CREDIT_PRICE_BLOCK = 'credit_price_block';

    const FIELD_ENABLE           = 'affect_product_credit_prices';
    const FIELD_PRICE_NAME       = 'price';
    const FIELD_PRICE_TYPE_NAME  = 'price_type_name';
    const FIELD_SORT_ORDER_NAME  = 'sort_order';
    const FIELD_OPTION_ID        = 'option_id';
    const FIELD_TYPE_NAME        = 'credit_price_type';
    const FIELD_CREDITS_NAME     = 'credits';
    const FIELD_MIN_CREDITS_NAME = 'min_credits';
    const FIELD_MAX_CREDITS_NAME = 'max_credits';

    const PRICE_TYPE_SINGLE = 'single';
    const PRICE_TYPE_RANGE  = 'range';
    const PRICE_TYPE_FIXED  = 'fixed';

    const CUSTOM_OPTIONS_LISTING = 'product_custom_options_listing';

    const PRICE_OPTION = 'credit_price_options';

    const GRID_TYPE_SELECT_NAME = 'values';

    const GROUP_CUSTOM_OPTIONS_PREVIOUS_NAME      = 'product-details';
    const GROUP_CUSTOM_OPTIONS_DEFAULT_SORT_ORDER = 11;

    const CONTAINER_PRICE_TYPE         = 'container_price_type';
    const CONTAINER_TYPE_STATIC_NAME   = 'container_type_static';
    const CONTAINER_TYPE_STATIC_FIELDS = 'container_type_fields_static';
    const CONTAINER_PRICE_GRID_NAME    = 'container_price_grid';
    const CONTAINER_SINGLE_PRICE_NAME  = 'container_single_price';
    const CONTAINER_RANGE_PRICE_NAME   = 'container_range_price';

    const BUTTON_ADD = 'button_add';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var LocatorInterface
     */
    private $locator;

    public function __construct(
        ProductOptionCreditFactory $productOptionCreditFactory,
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager
    ) {
        $this->productOptionCreditFactory = $productOptionCreditFactory;
        $this->locator                    = $locator;
        $this->storeManager               = $storeManager;
        $this->productOptionsConfig       = $productOptionsConfig;
        $this->productOptionsPrice        = $productOptionsPrice;
        $this->urlBuilder                 = $urlBuilder;
        $this->arrayManager               = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->locator->getProduct()->getTypeId() !== CreditType::TYPE_CREDITPOINTS) {
            return $data;
        }
        $productId = $this->locator->getProduct()->getId();

        $productOptions = $this->getProductOptions($productId);

        $options = [];
        foreach ($productOptions as $option) {
            $options[] = $this->prepareOptions($option);
        }

        if ($productOptions->getFirstItem()->getOptionPriceOptions() == static::PRICE_TYPE_FIXED) {
            $options = [
                static::FIELD_ENABLE      => 1,
                static::DATA_CREDIT_SCOPE => [
                    static::CONTAINER_PRICE_GRID_NAME => $options
                ]
            ];
        } elseif ($options) {
            $options = array_replace_recursive(
                [static::DATA_CREDIT_SCOPE => $options[0]],
                [static::FIELD_ENABLE => 1]
            );
        }

        return array_replace_recursive(
            $data,
            [
                $productId => $options,
                $productId => [
                    static::DATA_SOURCE_DEFAULT => $options,
                ]
            ]
        );
    }

    /**
     * @param \Mirasvit\Credit\Model\ProductOptionCredit $options
     * @return array
     */
    private function prepareOptions($options)
    {
        return [
            static::FIELD_OPTION_ID        => $options->getId(),
            static::FIELD_PRICE_NAME       => $options->getOptionPrice(),
            static::FIELD_TYPE_NAME        => $options->getOptionPriceOptions(),
            static::FIELD_PRICE_TYPE_NAME  => $options->getOptionPriceType(),
            static::FIELD_CREDITS_NAME     => $options->getOptionCredits(),
            static::FIELD_MIN_CREDITS_NAME => $options->getOptionMinCredits(),
            static::FIELD_MAX_CREDITS_NAME => $options->getOptionMaxCredits(),
        ];


    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() !== CreditType::TYPE_CREDITPOINTS) {
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
                static::CREDIT_PRICE_BLOCK => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label'         => __('Credit Price Options'),
                                'collapsible'   => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope'     => static::DATA_SCOPE.'.'.static::DATA_CREDIT_SCOPE,
                                'sortOrder'     => $this->getNextGroupSortOrder(
                                    $meta,
                                    static::GROUP_CUSTOM_OPTIONS_PREVIOUS_NAME,
                                    static::GROUP_CUSTOM_OPTIONS_DEFAULT_SORT_ORDER
                                ),
                            ],
                        ],
                    ],
                    'children' => [
                        static::CONTAINER_PRICE_TYPE         => $this->getPriceTypeField(10),
                        static::CONTAINER_TYPE_STATIC_FIELDS => $this->getStaticTypeFieldsContainerConfig(70),
                        static::CONTAINER_PRICE_GRID_NAME    => $this->getPriceGridContainerConfig(150),
                        static::CONTAINER_SINGLE_PRICE_NAME  => $this->singleContainer(160),
                        static::CONTAINER_RANGE_PRICE_NAME   => $this->rangeContainer(170),
                        static::FIELD_OPTION_ID              => $this->getOptionIdFieldConfig(140),
                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    public function singleContainer($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'     => Container::NAME,
                        'component'         => 'Magento_Ui/js/form/components/group',
                        'breakLine'         => false,
                        'showLabel'         => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                        'sortOrder'         => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_PRICE_NAME      => $this->getPriceFieldConfig(100),
                static::FIELD_PRICE_TYPE_NAME => $this->getPriceTypeFieldConfig(20),
                static::FIELD_CREDITS_NAME    => $this->getCreditsFieldConfig(130),
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    public function rangeContainer($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'     => Container::NAME,
                        'component'         => 'Magento_Ui/js/form/components/group',
                        'breakLine'         => false,
                        'showLabel'         => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                        'sortOrder'         => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_PRICE_NAME       => $this->getPriceFieldConfig(100),
                static::FIELD_MIN_CREDITS_NAME => $this->getMinCreditsFieldConfig(110),
                static::FIELD_MAX_CREDITS_NAME => $this->getMaxCreditsFieldConfig(120),
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getPriceTypeField($sortOrder)
    {
        $option = $this->getProductOptions($this->locator->getProduct()->getId())->getFirstItem();
        $value = $option->getOptionPriceOptions() ?: [];

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'                => __('Price Type'),
                        'componentType'        => Field::NAME,
                        'formElement'          => Select::NAME,
                        'component'            => 'Mirasvit_Credit/js/product-price-options-type',
                        'elementTmpl'          => 'ui/grid/filters/elements/ui-select',
                        'dataScope'            => static::FIELD_TYPE_NAME,
                        'dataType'             => Text::NAME,
                        'sortOrder'            => $sortOrder,
                        'value'                => $value,
                        'options'              => $this->getProductPriceOptionTypes(),
                        'disableLabel'         => true,
                        'multiple'             => false,
                        'selectedPlaceholders' => [
                            'defaultPlaceholder' => __('-- Please select --'),
                        ],
                        'validation'           => [
                            'required-entry' => true
                        ],
                        'groupsConfig'         => [
                            self::PRICE_TYPE_SINGLE => [
                                'values'  => [self::PRICE_TYPE_SINGLE],
                                'indexes' => [
                                    static::CONTAINER_SINGLE_PRICE_NAME,
                                    static::FIELD_PRICE_NAME,
                                    static::FIELD_PRICE_TYPE_NAME,
                                    static::FIELD_CREDITS_NAME,
                                ]
                            ],
                            self::PRICE_TYPE_RANGE => [
                                'values'  => [self::PRICE_TYPE_RANGE],
                                'indexes' => [
                                    static::CONTAINER_RANGE_PRICE_NAME,
                                    static::FIELD_PRICE_NAME,
                                    static::FIELD_MIN_CREDITS_NAME,
                                    static::FIELD_MAX_CREDITS_NAME,
                                ]
                            ],
                            self::PRICE_TYPE_FIXED => [
                                'values'  => [self::PRICE_TYPE_FIXED],
                                'indexes' => [
                                    static::BUTTON_ADD,
                                    static::CONTAINER_PRICE_GRID_NAME,
                                    static::FIELD_PRICE_NAME,
                                    static::FIELD_PRICE_TYPE_NAME,
                                    static::FIELD_CREDITS_NAME,
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getStaticTypeContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType'     => Container::NAME,
                        'component'         => 'Magento_Ui/js/form/components/group',
                        'breakLine'         => false,
                        'showLabel'         => false,
                        'additionalClasses' => 'admin__field-group-columns admin__control-group-equal',
                        'sortOrder'         => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::FIELD_PRICE_NAME       => $this->getPriceFieldConfig(100),
                static::FIELD_PRICE_TYPE_NAME  => $this->getPriceTypeFieldConfig(20),
                static::FIELD_CREDITS_NAME     => $this->getCreditsFieldConfig(130),
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getStaticTypeFieldsContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => null,
                        'formElement'   => Container::NAME,
                        'componentType' => Container::NAME,
                        'template'      => 'ui/form/components/complex',
                        'sortOrder'     => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                static::BUTTON_ADD => $this->getAddOptionButton(140),
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getPriceGridContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel'      => __('Add Option'),
                        'componentType'       => DynamicRows::NAME,
                        'component'           => 'Magento_Catalog/js/components/dynamic-rows-import-custom-options',
                        'template'            => 'Mirasvit_Credit/dynamic-rows/templates/option',
                        'additionalClasses'   => 'admin__field-wide',
                        'addButton'           => false,
                        'renderDefaultRecord' => false,
                        'columnsHeader'       => false,
                        'collapsibleHeader'   => false,
                        'sortOrder'           => $sortOrder,
                        'dataProvider'        => static::CUSTOM_OPTIONS_LISTING,
                        'imports'             => ['insertData' => '${ $.provider }:${ $.dataProvider }'],
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'headerLabel'      => null,
                                'componentType'    => Container::NAME,
                                'component'        => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => static::PRICE_OPTION . '.' . static::FIELD_SORT_ORDER_NAME,
                                'isTemplate'       => true,
                                'is_collection'    => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::PRICE_OPTION => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Fieldset::NAME,
                                        'label'         => null,
                                        'sortOrder'     => 10,
                                        'opened'        => true,
                                    ],
                                ],
                            ],
                            'children' => [
                                static::FIELD_SORT_ORDER_NAME => $this->getPositionFieldConfig(40),
                                static::GRID_TYPE_SELECT_NAME => $this->getStaticTypeContainerConfig(20),
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getPositionFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => static::FIELD_SORT_ORDER_NAME,
                        'dataType'      => Number::NAME,
                        'visible'       => false,
                        'sortOrder'     => $sortOrder,
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getProductPriceOptionTypes()
    {
        $options = [
            ['label' => __('Fixed'), 'value' => self::PRICE_TYPE_FIXED],
            ['label' => __('Range'), 'value' => self::PRICE_TYPE_RANGE],
            ['label' => __('Single'), 'value' => self::PRICE_TYPE_SINGLE],
        ];

        return $options;
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getPriceFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Price'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => static::FIELD_PRICE_NAME,
                        'dataType'      => Number::NAME,
                        'addbefore'     => $this->getCurrencySymbol(),
                        'sortOrder'     => $sortOrder,
                        'validation'    => [
                            'validate-zero-or-greater' => true
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getPriceTypeFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Price Options'),
                        'componentType' => Field::NAME,
                        'formElement'   => Select::NAME,
                        'dataScope'     => static::FIELD_PRICE_TYPE_NAME,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'options'       => $this->productOptionsPrice->toOptionArray(),
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getCreditsFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Credits'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => static::FIELD_CREDITS_NAME,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'validation'    => [
                            'validate-zero-or-greater' => true
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getMinCreditsFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Min Credits Amount'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => static::FIELD_MIN_CREDITS_NAME,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'validation'    => [
                            'validate-zero-or-greater' => true
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getMaxCreditsFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Max Credits Amount'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => static::FIELD_MAX_CREDITS_NAME,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'validation'    => [
                            'validate-zero-or-greater' => true
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getOptionIdFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement'   => Input::NAME,
                        'componentType' => Field::NAME,
                        'dataScope'     => static::FIELD_OPTION_ID,
                        'sortOrder'     => $sortOrder,
                        'visible'       => false,
                    ],
                ],
            ],
        ];
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getAddOptionButton($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'title'         => __('Add Option'),
                        'formElement'   => Container::NAME,
                        'componentType' => Container::NAME,
                        'visible'       => false,
                        'component'     => 'Magento_Ui/js/form/components/button',
                        'sortOrder'     => $sortOrder,
                        'dataScope'     => static::BUTTON_ADD,
                        'actions'       => [
                            [
                                'targetName' => 'ns = ${ $.ns }, index = ' . static::CONTAINER_PRICE_GRID_NAME,
                                'actionName' => 'processingAddChild',
                            ]
                        ]
                    ]
                ],
            ],
        ];
    }

    /**
     * Get currency symbol
     *
     * @return string
     */
    protected function getCurrencySymbol()
    {
        return $this->storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }

    /**
     * @param int $productId
     * @return \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\Collection
     */
    private function getProductOptions($productId)
    {
        if (empty($this->options)) {
            $this->options = $this->productOptionCreditFactory->create()->getCollection()->addProductFilter($productId);
        }

        return $this->options;
    }
}
