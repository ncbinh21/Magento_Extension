<?php

namespace Orange35\ImageConstructor\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions as OriginalCustomOptions;
use Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\CustomOptions as GroupedOptions;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Container;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Orange35\ImageConstructor\Helper\Image as HelperImage;

class CustomOptions extends OriginalCustomOptions implements ModifierInterface
{

    /**
     * Get config for "Option Type" field
     *
     * @param int $sortOrder
     * @return array
     */

    const FIELD_IMAGE_UPLOAD_NAME = 'layer';
    const FIELD_IMAGE_PREVIEW_NAME = 'layer_preview';
    const FIELD_IMAGE_DELETE_NAME = 'delete_image';

    const CONTAINER_HEADER_TOOLTIP = 'container_tooltip';

    /**
     * @var $helper HelperImage
     */
    protected $helper;
    /**
     * {@inheritdoc}
     * @param $helper HelperImage
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        HelperImage $helper

    )
    {
        parent::__construct($locator, $storeManager, $productOptionsConfig, $productOptionsPrice, $urlBuilder, $arrayManager);
        $this->helper = $helper;
    }

    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        if ($product->getTypeId() === GroupedOptions::PRODUCT_TYPE_GROUPED) {
            return $data;
        }

        $options = [];
        $productOptions = $product->getOptions() ?: [];

        /** @var \Magento\Catalog\Model\Product\Option $option */

        foreach ($productOptions as $index => $option) {
            $values = $option->getValues() ?: [];

            /** @var \Magento\Catalog\Model\Product\Option $value */
            foreach ($values as $value) {
                $valueData = $value->getData();
                if (!is_null($value->getLayer())){
                    $imageUrl = $this->helper->getBaseUrl() . HelperImage::IMAGE_PATH . $value->getLayer();
                    $valueData = array_replace_recursive(
                        $value->getData(),
                        [self::FIELD_IMAGE_UPLOAD_NAME => [0 => [
                            'name' => $value->getLayer(),
                            'url' => $imageUrl
                        ]]]
                    );
                }
                $options[$index][static::GRID_TYPE_SELECT_NAME][] = $valueData;
            }
        }
        return array_replace_recursive(
            $data,
            [
                $this->locator->getProduct()->getId() => [
                    static::DATA_SOURCE_DEFAULT => [
                        static::FIELD_ENABLE => 1,
                        static::GRID_OPTIONS_NAME => $options
                    ]
                ]
            ]
        );

    }
    
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() === GroupedOptions::PRODUCT_TYPE_GROUPED) {
            return $meta;
        }

        $this->meta = $meta;

        $this->createCustomOptionsPanel();

        return $this->meta;
    }

    protected function createCustomOptionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_CUSTOM_OPTIONS_NAME => [
                    'children' => [
                        static::CONTAINER_HEADER_TOOLTIP => $this->getHeaderContainerConfig(11),
                        static::GRID_OPTIONS_NAME => $this->getOptionsGridConfig(30)
                    ]
                ]
            ]
        );
        return $this;
    }

    protected function getHeaderContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'Orange35_ImageConstructor/tooltip',
                        'sortOrder' => $sortOrder,
                        'content' => __('Please make sure that the images you upload for Image Layer are of the same exact size as the product image itself, otherwise they won\'t be correctly positioned!'),
                    ],
                ],
            ],
        ];
    }


    protected function getOptionsGridConfig($sortOrder)
    {
        return [
            'children' => [
                'record' => [
                    'children' => [
                        static::CONTAINER_OPTION => [
                            'children' => [
                                static::GRID_TYPE_SELECT_NAME => $this->getSelectTypeGridConfig(30)
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }


    protected function getSelectTypeGridConfig($sortOrder)
    {
        $layersGrid = [
            'children' => [
                'record' => [
                    'children' => [
                        static::FIELD_IMAGE_UPLOAD_NAME => $this->getImgConfig(90),
                    ]
                ]
            ]
        ];
        return $layersGrid;
    }


    protected function getImgConfig($sortOrder)
    {

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Image Layer'),
                        'componentType' =>  'field',
                        'formElement' => 'fileUploader',
                        'dataScope' => static::FIELD_IMAGE_UPLOAD_NAME,
                        'dataType' => 'file',
                        'fileInputName' => 'layer',
                        'sortOrder' => $sortOrder,
                        'isMultipleFiles' => false,
                        'component' => 'Orange35_ImageConstructor/js/components/file-uploader',
                        'template' => 'Orange35_ImageConstructor/components/uploader',
                        'previewTmpl' => 'Orange35_ImageConstructor/image-preview',
                        'uploaderConfig' => [
                            'url' => 'o35_image_constructor/layer/upload/field/layer',
                            'baseUrl' => $this->helper->getBaseUrl() . $this->helper->getBasePath(),
                        ]
                    ],
                ],
            ],
        ];
    }
}