<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Directory\Helper\Data;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Container;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

/**
 * Class Amounts
 *
 * @package Aheadworks\Giftcard\Ui\DataProvider\Product\Form\Modifier
 */
class Amounts extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Data
     */
    private $directoryHelper;

    /**
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param ArrayManager $arrayManager
     * @param Data $directoryHelper
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ArrayManager $arrayManager,
        Data $directoryHelper
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->arrayManager = $arrayManager;
        $this->directoryHelper = $directoryHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->modifyAmountsContainer($meta);
        $this->modifyAllowOpenAmountField($meta);
        $this->modifyOpenAmountMinFields($meta);
        $this->modifyOpenAmountMaxFields($meta);

        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Modify amounts container
     *
     * @param array $meta
     * @return $this
     */
    private function modifyAmountsContainer(array &$meta)
    {
        $fieldName = static::CONTAINER_PREFIX . ProductAttributeInterface::CODE_AW_GC_AMOUNTS;
        if (!$this->getGroupCodeByField($meta, $fieldName)) {
            return $this;
        }
        $containerPath = $this->arrayManager->findPath($fieldName, $meta);
        $meta = $this->arrayManager->set(
            $containerPath . '/children/' . ProductAttributeInterface::CODE_AW_GC_AMOUNTS,
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'dynamicRows',
                            'label' => __('Amounts'),
                            'required' => 1,
                            'renderDefaultRecord' => false,
                            'recordTemplate' => 'record',
                            'dataScope' => '',
                            'dndConfig' => [
                                'enabled' => false,
                            ],
                            'disabled' => false,
                        ],
                    ],
                ],
                'children' => [
                    'record' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'componentType' => Container::NAME,
                                    'isTemplate' => true,
                                    'is_collection' => true,
                                    'component' => 'Magento_Ui/js/dynamic-rows/record',
                                    'dataScope' => '',
                                ],
                            ],
                        ],
                        'children' => [
                            'website_id' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'dataType' => Text::NAME,
                                            'formElement' => Select::NAME,
                                            'componentType' => Field::NAME,
                                            'dataScope' => 'website_id',
                                            'label' => __('Website'),
                                            'options' => $this->getWebsites(),
                                            'value' => $this->getDefaultWebsite(),
                                        ],
                                    ],
                                ],
                            ],
                            'price' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => Field::NAME,
                                            'formElement' => Input::NAME,
                                            'dataType' => Price::NAME,
                                            'label' => __('Amount'),
                                            'validation' => [
                                                'validate-zero-or-greater' => true,
                                                'validate-no-empty' => true,
                                            ],
                                            'enableLabel' => true,
                                            'dataScope' => 'price',
                                        ],
                                    ],
                                ],
                            ],
                            'actionDelete' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'fit' => true,
                                            'componentType' => 'actionDelete',
                                            'dataType' => Text::NAME,
                                            'label' => __('Action'),
                                            'dataScope' => 'delete'
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $this;
    }

    /**
     * Modify allow open amount field
     *
     * @param array $meta
     * @return $this
     */
    private function modifyAllowOpenAmountField(array &$meta)
    {
        $allowOpenAmountPath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_AW_GC_ALLOW_OPEN_AMOUNT,
            $meta,
            null,
            'children'
        );
        if (!$allowOpenAmountPath) {
            return $this;
        }
        $meta = $this->arrayManager->merge(
            $allowOpenAmountPath . static::META_CONFIG_PATH,
            $meta,
            [
                'switcherConfig' => [
                    'enabled' => true,
                    'rules' => [
                        [
                            'value' => '0',
                            'actions' => [
                                [
                                    'target' => 'product_form.product_form.gift-card-information.'
                                        . 'container_aw_gc_open_amount_min.aw_gc_open_amount_min',
                                    'callback' => 'hide'
                                ],
                                [
                                    'target' => 'product_form.product_form.gift-card-information.'
                                        . 'container_aw_gc_open_amount_max.aw_gc_open_amount_max',
                                    'callback' => 'hide'
                                ]
                            ]
                        ],
                        [
                            'value' => '1',
                            'actions' => [
                                [
                                    'target' => 'product_form.product_form.gift-card-information.'
                                        . 'container_aw_gc_open_amount_min.aw_gc_open_amount_min',
                                    'callback' => 'show'
                                ],
                                [
                                    'target' => 'product_form.product_form.gift-card-information.'
                                        . 'container_aw_gc_open_amount_max.aw_gc_open_amount_max',
                                    'callback' => 'show'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * Modify open amount min fields
     *
     * @param array $meta
     * @return $this
     */
    private function modifyOpenAmountMinFields(array &$meta)
    {
        $openAmountMinPath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MIN,
            $meta,
            null,
            'children'
        );
        if (!$openAmountMinPath) {
            return $this;
        }
        $meta = $this->arrayManager->merge(
            $openAmountMinPath . static::META_CONFIG_PATH,
            $meta,
            [
                'validation' => [
                    'required-entry' => true
                ]
            ]
        );

        return $this;
    }

    /**
     * Modify open amount max fields
     *
     * @param array $meta
     * @return $this
     */
    private function modifyOpenAmountMaxFields(array &$meta)
    {
        $openAmountMaxPath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_AW_GC_OPEN_AMOUNT_MAX,
            $meta,
            null,
            'children'
        );
        if (!$openAmountMaxPath) {
            return $this;
        }
        $meta = $this->arrayManager->merge(
            $openAmountMaxPath . static::META_CONFIG_PATH,
            $meta,
            [
                'validation' => [
                    'required-entry' => true
                ]
            ]
        );

        return $this;
    }

    /**
     * Retrieve websites list
     *
     * @return []
     */
    private function getWebsites()
    {
        $websites = [
            [
                'label' => __('All Websites') . ' [' . $this->directoryHelper->getBaseCurrencyCode() . ']',
                'value' => 0,
            ]
        ];
        $productWebsiteIds = $this->locator->getProduct()->getWebsiteIds();
        $websitesList = $this->storeManager->getWebsites();
        foreach ($websitesList as $website) {
            /** @var \Magento\Store\Model\Website $website */
            if (!in_array($website->getId(), $productWebsiteIds)) {
                continue;
            }
            $websites[] = [
                'label' => $website->getName() . ' [' . $website->getBaseCurrencyCode() . ']',
                'value' => $website->getId(),
            ];
        }
        return $websites;
    }

    /**
     * Retrieve default value for website
     *
     * @return int
     */
    private function getDefaultWebsite()
    {
        return $this->storeManager->getStore($this->locator->getProduct()->getStoreId())->getWebsiteId();
    }
}
