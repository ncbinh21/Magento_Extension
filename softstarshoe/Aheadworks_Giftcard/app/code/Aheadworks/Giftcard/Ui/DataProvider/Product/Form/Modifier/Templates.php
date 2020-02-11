<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\System\Store as SystemStore;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Container;
use Aheadworks\Giftcard\Model\Source\Entity\Attribute\GiftcardEmailTemplate;
use Magento\Framework\File\Size as FileSize;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

/**
 * Class Templates
 *
 * @package Aheadworks\Giftcard\Ui\DataProvider\Product\Form\Modifier
 */
class Templates extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var SystemStore
     */
    private $systemStore;

    /**
     * @var GiftcardEmailTemplate
     */
    private $giftCardEmailTemplateSource;

    /**
     * @var FileSize
     */
    private $fileSize;

    /**
     * @var MediaConfig
     */
    private $mediaConfig;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param SystemStore $systemStore
     * @param GiftcardEmailTemplate $giftCardEmailTemplateSource
     * @param FileSize $fileSize
     * @param MediaConfig $mediaConfig
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        SystemStore $systemStore,
        GiftcardEmailTemplate $giftCardEmailTemplateSource,
        FileSize $fileSize,
        MediaConfig $mediaConfig
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->systemStore = $systemStore;
        $this->giftCardEmailTemplateSource = $giftCardEmailTemplateSource;
        $this->fileSize = $fileSize;
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $fieldName = static::CONTAINER_PREFIX . ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES;
        if (!$this->getGroupCodeByField($meta, $fieldName)) {
            return $meta;
        }
        $containerPath = $this->arrayManager->findPath($fieldName, $meta);

        $meta = $this->arrayManager->set(
            $containerPath . '/children/' . ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES,
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'dynamicRows',
                            'component' => 'Aheadworks_Giftcard/js/ui/dynamic-rows/dynamic-rows',
                            'label' => __('Email Templates'),
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
                            'store_id' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'dataType' => Text::NAME,
                                            'formElement' => Select::NAME,
                                            'componentType' => Field::NAME,
                                            'dataScope' => 'store_id',
                                            'label' => __('Store'),
                                            'options' => $this->getStores(),
                                        ],
                                    ],
                                ],
                            ],
                            'template' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'dataType' => Text::NAME,
                                            'formElement' => Select::NAME,
                                            'componentType' => Field::NAME,
                                            'dataScope' => 'template',
                                            'label' => __('Email Template'),
                                            'options' => $this->getTemplates(),
                                        ],
                                    ],
                                ],
                            ],
                            // @codingStandardsIgnoreStart
                            'image' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'label' => __('Image'),
                                            'componentType' => 'fileUploader',
                                            'formElement' => 'fileUploader',
                                            'component' => 'Aheadworks_Giftcard/js/ui/form/element/product/email-templates/image-uploader',
                                            'template' => 'Aheadworks_Giftcard/ui/form/element/product/email-templates/image-uploader',
                                            'previewTmpl' => 'Aheadworks_Giftcard/ui/form/element/product/email-templates/image-preview',
                                            'imagePlaceholderText' => __('Click here or drag and drop to add images.'),
                                            'allowedExtensions' => 'jpg jpeg gif png',
                                            'uploaderConfig' => [
                                                'url' => 'aw_giftcard_admin/product/imageUpload'
                                            ],
                                            'isMultipleFiles' => false,
                                            'maxFileSize' => $this->getFileMaxSize(),
                                            'fileInputName' => 'image',
                                            'dataScope' => 'image'
                                        ],
                                    ],
                                ],
                            ],
                            // @codingStandardsIgnoreEnd
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

        $cardTypePath = $this->arrayManager->findPath(
            ProductAttributeInterface::CODE_AW_GC_TYPE,
            $meta,
            null,
            'children'
        );
        // @codingStandardsIgnoreStart
        if ($cardTypePath) {
            $meta = $this->arrayManager->merge(
                $cardTypePath . static::META_CONFIG_PATH,
                $meta,
                [
                    'switcherConfig' => [
                        'enabled' => true,
                        'rules' => [
                            [
                                'value' => '1',
                                'actions' => [
                                    [
                                        'target' => 'product_form.product_form.gift-card-information.container_aw_gc_email_templates.aw_gc_email_templates',
                                        'callback' => 'show'
                                    ],
                                    [
                                        'target' => 'product_form.product_form.gift-card-information.container_aw_gc_days_order_delivery.aw_gc_days_order_delivery',
                                        'callback' => 'hide'
                                    ]
                                ]
                            ],
                            [
                                'value' => '2',
                                'actions' => [
                                    [
                                        'target' => 'product_form.product_form.gift-card-information.container_aw_gc_email_templates.aw_gc_email_templates',
                                        'callback' => 'hide'
                                    ],
                                    [
                                        'target' => 'product_form.product_form.gift-card-information.container_aw_gc_days_order_delivery.aw_gc_days_order_delivery',
                                        'callback' => 'show'
                                    ]
                                ]
                            ],
                            [
                                'value' => '3',
                                'actions' => [
                                    [
                                        'target' => 'product_form.product_form.gift-card-information.container_aw_gc_email_templates.aw_gc_email_templates',
                                        'callback' => 'show'
                                    ],
                                    [
                                        'target' => 'product_form.product_form.gift-card-information.container_aw_gc_days_order_delivery.aw_gc_days_order_delivery',
                                        'callback' => 'show'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }
        // @codingStandardsIgnoreEnd
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
        $product = $this->locator->getProduct();
        $modelId = $product->getId();
        if (isset($data[$modelId][self::DATA_SOURCE_DEFAULT]['aw_gc_email_templates'])
            && is_array($data[$modelId][self::DATA_SOURCE_DEFAULT]['aw_gc_email_templates'])
        ) {
            foreach ($data[$modelId][self::DATA_SOURCE_DEFAULT]['aw_gc_email_templates'] as $index => $template) {
                if (isset($template['image'])) {
                    $data[$modelId][self::DATA_SOURCE_DEFAULT]['aw_gc_email_templates'][$index]['image'] = [
                        'file' => $template['image'],
                        'name' => '',
                        'url' => $template['image'] ? $this->mediaConfig->getTmpMediaUrl($template['image']) : ''
                    ];
                }
            }
        }
        return $data;
    }

    /**
     * Retrieve stores list
     *
     * @return []
     */
    private function getStores()
    {
        $storeValues = $this->systemStore->getStoreValuesForForm(false, true);
        return $storeValues;
    }

    /**
     * Retrieve templates list
     *
     * @return []
     */
    private function getTemplates()
    {
        $templateValues = $this->giftCardEmailTemplateSource->toOptionArray();
        return $templateValues;
    }

    /**
     * Retrieve file max size
     *
     * @return int
     */
    private function getFileMaxSize()
    {
        return $this->fileSize->getMaxFileSize();
    }
}
