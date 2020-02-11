<?php

namespace Orange35\Colorpickercustom\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions as OriginalCustomOptions;
use Magento\GroupedProduct\Ui\DataProvider\Product\Form\Modifier\CustomOptions as GroupedOptions;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\ProductOptions\ConfigInterface;
use Magento\Catalog\Model\Config\Source\Product\Options\Price as ProductOptionsPrice;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Orange35\Colorpickercustom\Helper\Image as ImageHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CustomOptions extends OriginalCustomOptions
{
    const FIELD_IMAGE_UPLOAD_NAME   = 'image';
    const FIELD_IMAGE_PREVIEW_NAME  = 'image_preview';
    const FIELD_IMAGE_DELETE_NAME   = 'delete_image';
    const FIELD_COLOR_NAME          = 'color';
    const FIELD_IS_COLORPICKER_NAME = 'is_colorpicker';
    const FIELD_TOOLTIP_NAME        = 'tooltip';
    const FIELD_WIDTH_NAME          = 'swatch_width';
    const FIELD_HEIGHT_NAME         = 'swatch_height';

    protected $imageHelper;
    protected $scopeConfig;

    /**
     * Default swatch width
     * @var
     */
    protected $width;

    /**
     * Default swatch height
     * @var
     */
    protected $height;

    /**
     * CustomOptions constructor.
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $productOptionsConfig
     * @param ProductOptionsPrice $productOptionsPrice
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param ImageHelper $helper
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        ConfigInterface $productOptionsConfig,
        ProductOptionsPrice $productOptionsPrice,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        ImageHelper $helper,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->imageHelper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->width = $this->scopeConfig->getValue(
            'colorpickercustom_section/swatch/swatch_width',
            ScopeInterface::SCOPE_STORE
        );
        $this->height = $this->scopeConfig->getValue(
            'colorpickercustom_section/swatch/swatch_height',
            ScopeInterface::SCOPE_STORE
        );
        parent::__construct($locator, $storeManager, $productOptionsConfig, $productOptionsPrice, $urlBuilder, $arrayManager);
    }

    /**
     * @param array $data
     * @return array
     */
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
                if (!is_null($value->getImage())) {
                    $imageUrl = $this->imageHelper->getImageUrl($value->getImage());
                    $valueData = array_replace_recursive(
                        $value->getData(),
                        [self::FIELD_IMAGE_UPLOAD_NAME => [0 => [
                            'name' => $value->getImage(),
                            'url'  => $imageUrl,
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
                        static::FIELD_ENABLE      => 1,
                        static::GRID_OPTIONS_NAME => $options,
                    ],
                ],
            ]
        );
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getTypeId() === GroupedOptions::PRODUCT_TYPE_GROUPED) {
            return $meta;
        }

        $this->meta = $meta;
        $this->createCustomOptionsPanel();
        $this->setColumnOrder('is_delete', 1000);
        return $this->meta;
    }

    /**
     * @param $name
     * @param $order
     * return void
     */
    private function setColumnOrder($name, $order)
    {
        $columns = &$this->meta[static::GROUP_CUSTOM_OPTIONS_NAME]['children'][static::GRID_OPTIONS_NAME]['children']['record']['children'][static::CONTAINER_OPTION]['children'][static::GRID_TYPE_SELECT_NAME]['children']['record']['children'];
        $columns[$name]['arguments']['data']['config']['sortOrder'] = $order;
        $this->sortColumns($columns);
    }

    private function sortColumns(array &$columns)
    {
        uksort($columns, function ($key1, $key2) use ($columns) {
            return $columns[$key1]['arguments']['data']['config']['sortOrder'] -
                $columns[$key2]['arguments']['data']['config']['sortOrder'];
        });
    }

    /**
     * @return $this
     */
    protected function createCustomOptionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_CUSTOM_OPTIONS_NAME => [
                    'children' => [
                        static::GRID_OPTIONS_NAME => $this->getOptionsGridConfig(30),
                    ],
                ],
            ]
        );

        return $this;
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getOptionsGridConfig($sortOrder)
    {
        return [
            'children' => [
                'record' => [
                    'children' => [
                        static::CONTAINER_OPTION => [
                            'children' => [
                                static::CONTAINER_COMMON_NAME => $this->getCommonContainerConfig(10),
                                static::GRID_TYPE_SELECT_NAME => $this->getSelectTypeGridConfig(30),
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
    protected function getSelectTypeGridConfig($sortOrder)
    {
        $imagesGrid = [
            'children' => [
                'record' => [
                    'children' => [
                        static::FIELD_IMAGE_UPLOAD_NAME => $this->getImgConfig(70),
                        static::FIELD_COLOR_NAME        => $this->getColorpickerConfig(80),
                    ],
                ],
            ],
        ];

        return $imagesGrid;
    }

    /**
     * @param int $sortOrder
     * @return array
     */
    protected function getCommonContainerConfig($sortOrder)
    {
        return [
            'children' => [
                static::FIELD_IS_COLORPICKER_NAME => $this->getIsColorpickerConfig(50),
                static::FIELD_TOOLTIP_NAME        => $this->getTooltipConfig(60),
                static::FIELD_WIDTH_NAME          => $this->getWidthConfig(70),
                static::FIELD_HEIGHT_NAME         => $this->getHeightConfig(80),
            ],
        ];
    }


    /**
     * @param $sortOrder
     * @return array
     */
    protected function getIsColorpickerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Colorpicker'),
                        'componentType' => Field::NAME,
                        'formElement'   => Checkbox::NAME,
                        'dataScope'     => 'is_colorpicker',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'value'         => '0',
                        'valueMap'      => [
                            'true'  => '1',
                            'false' => '0',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getTooltipConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Tooltip Type'),
                        'componentType' => Field::NAME,
                        'formElement'   => Select::NAME,
                        'dataScope'     => 'tooltip',
                        'dataType'      => Number::NAME,
                        'sortOrder'     => $sortOrder,
                        'options'       => [
                            ['value' => '0', 'label' => __('Disabled')],
                            ['value' => '1', 'label' => __('Text')],
                            ['value' => '2', 'label' => __('Image & Text')],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getWidthConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Swatch width'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => 'swatch_width',
                        'dataType'      => Number::NAME,
                        'sortOrder'     => $sortOrder,
                        'value'         => $this->width,
                        'validation'    => [
                            'validate-greater-than-zero' => true,
                            'validate-number'            => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getHeightConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Swatch height'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => 'swatch_height',
                        'dataType'      => Number::NAME,
                        'sortOrder'     => $sortOrder,
                        'value'         => $this->height,
                        'validation'    => [
                            'validate-greater-than-zero' => true,
                            'validate-number'            => true,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getImgConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'           => __('Swatch Image'),
                        'componentType'   => 'field',
                        'formElement'     => 'fileUploader',
                        'dataScope'       => static::FIELD_IMAGE_UPLOAD_NAME,
                        'dataType'        => 'file',
                        'fileInputName'   => 'image',
                        'sortOrder'       => $sortOrder,
                        'isMultipleFiles' => false,
                        'component'       => 'Orange35_Colorpickercustom/js/components/file-uploader',
                        'template'        => 'Orange35_Colorpickercustom/components/uploader',
                        'previewTmpl'     => 'Orange35_Colorpickercustom/image-preview',
                        'uploaderConfig'  => [
                            'url'      => 'o35_colorpicker_custom/image/upload/field/image',
                            'imageUrl' => $this->imageHelper->getBaseUrl() . $this->imageHelper->getBasePath(),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param $sortOrder
     * @return array
     */
    protected function getColorpickerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __('Color'),
                        'componentType' => Field::NAME,
                        'formElement'   => Input::NAME,
                        'dataScope'     => 'color',
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'template'      => 'Orange35_Colorpickercustom/components/colorpicker',
                        'component'     => 'Orange35_Colorpickercustom/js/components/colorpicker',
                    ],
                ],
            ],
        ];
    }
}