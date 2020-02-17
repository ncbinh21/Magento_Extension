<?php

namespace Forix\Configurable\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;
use \Forix\Configurable\Model\Attribute\Source\AttributeTemplate;
use \Magento\Catalog\Api\Data\ProductInterface as Product;
use Magento\Framework\App\Helper\Context;
use \Forix\Configurable\Model\RadioSwatchAttributesProvider;
use Magento\Catalog\Helper\Product as CatalogProduct;

class RadioSwatchHelper extends AbstractHelper
{
    const EMPTY_IMAGE_VALUE = 'no_selection';
    const RADIO_SWATCH_ATTRIBUTE_CODE = 'mb_option_image';
    const RADIO_SWATCH_IMAGE_NAME = 'mb_option_image';
    const RADIO_SWATCH_THUMBNAIL_NAME = 'mb_option_image_thumb';
    /**
     * @var RadioSwatchAttributesProvider
     */
    protected $_radioSwatchAttributesProvider;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    protected $_catalogProduct;

    protected $_configurableHelper;

    public function __construct(
        Context $context,
        \Magento\Catalog\Helper\Image $imageHelper,
        CatalogProduct $catalogProduct,
        \Magento\ConfigurableProduct\Helper\Data $configurableHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        RadioSwatchAttributesProvider $radioSwatchAttributesProvider
    )
    {
        parent::__construct($context);
        $this->_catalogProduct = $catalogProduct;
        $this->_configurableHelper = $configurableHelper;
        $this->_imageHelper = $imageHelper;
        $this->_storeManager = $storeManager;
        $this->_radioSwatchAttributesProvider = $radioSwatchAttributesProvider;
    }

    /**
     * @param Product $product
     * @param string $imageType
     * @return bool
     */
    protected function isProductHasImage(Product $product, $imageType)
    {
        return $product->getData($imageType) !== null && $product->getData($imageType) != self::EMPTY_IMAGE_VALUE;
    }

    /**
     * @param Product $childProduct
     * @param string $imageType
     * @return string
     */
    protected function getRadioSwatchProductImage(Product $childProduct, $imageType)
    {
        $imageAttributes = [];
        if ($this->isProductHasImage($childProduct, self::RADIO_SWATCH_ATTRIBUTE_CODE)) {
            $swatchImageId = $imageType;
            $imageAttributes = ['type' => self::RADIO_SWATCH_ATTRIBUTE_CODE];
        } elseif ($this->isProductHasImage($childProduct, 'image')) {
            $swatchImageId = 'radio_swatch_image_base';
            $imageAttributes = ['type' => 'image'];
        }
        if (isset($swatchImageId)) {
            return $this->_imageHelper->init($childProduct, $swatchImageId, $imageAttributes)->getUrl();
        }
        return '';
    }

    /**
     * Retrieve all visible Swatch attributes for current product.
     *
     * @param Product $product
     * @return array
     */
    public function getRadioSwatchAttributesAsArray(Product $product)
    {
        $result = [];
        $radioSwatchAttributes = $this->getRadioSwatchAttributes($product);
        foreach ($radioSwatchAttributes as $radioSwatchAttribute) {
            $radioSwatchAttribute->setStoreId($this->_storeManager->getStore()->getId());
            $attributeData = $radioSwatchAttribute->getData();
            foreach ($radioSwatchAttribute->getSource()->getAllOptions(false) as $option) {
                $attributeData['options'][$option['value']] = $option['label'];
            }
            $result[$attributeData['attribute_id']] = $attributeData;
        }

        return $result;
    }


    /**
     * Get Swatch config data
     * @param Product $product
     * @return array
     */
    public function getRadioSwatchConfig($product)
    {
        $attributesData = $this->getRadioSwatchAttributesAsArray($product);
        $config = [];
        foreach ($attributesData as $attributeId => $attributeDataArray) {
            if (isset($attributeDataArray['options'])) {
                $config[$attributeId] = $this->addRadioSwatchDataForAttribute(
                    $product,
                    $attributeDataArray['options'],
                    $attributeDataArray
                );
            }
        }

        return $config;
    }


    /**
     * Add Swatch Data for attribute
     *
     * @param Product $product
     * @param array $options
     * @param array $attributeDataArray
     * @return array
     */
    protected function addRadioSwatchDataForAttribute($product, array $options, array $attributeDataArray)
    {
        $result = [];
        foreach ($options as $optionId => $label) {
            $result[$optionId]['value'] = $label;
            $result[$optionId] = $this->addAdditionalMediaData($product, $result[$optionId], $optionId, $attributeDataArray);
            $result[$optionId]['label'] = $label;
        }
        return $result;
    }

    /**
     * Add media from variation
     *
     * @param Product $configurableProduct
     * @param array $radioSwatch
     * @param integer $optionId
     * @param array $attributeDataArray
     * @return array
     */
    protected function addAdditionalMediaData($configurableProduct, array $radioSwatch, $optionId, array $attributeDataArray)
    {
    	if ($attributeDataArray["option_template"] == AttributeTemplate::RAIDO_OPTION_ONLY) {
		    $radioSwatch['type'] = 'radio_only';
	    } else {
            $variationMedia = $this->getVariationMedia($configurableProduct, $attributeDataArray['attribute_code'], $optionId);
            $radioSwatch['type'] = 'radio_swatch';// AttributeTemplate::RADIO_WITH_SWATCH;
            if (!empty($variationMedia)) {
			    $radioSwatch = array_merge($radioSwatch, $variationMedia);
		    }
	    }

        return $radioSwatch;
    }

    /**
     * Generate Product Media array
     *
     * @param Product $configurableProduct
     * @param string $attributeCode
     * @param integer $optionId
     * @return array
     */
    protected function getVariationMedia($configurableProduct, $attributeCode, $optionId)
    {
        $variationProduct = $this->loadFirstVariationWithRadioSwatchImage(
            $configurableProduct,
            [$attributeCode => $optionId]
        );

        if (!$variationProduct) {
            $variationProduct = $this->loadFirstVariationWithImage(
                $configurableProduct,
                [$attributeCode => $optionId]
            );
        }

        $variationMediaArray = [];
        if ($variationProduct) {
            $variationMediaArray = [
                'value' => $this->getRadioSwatchProductImage($variationProduct, self::RADIO_SWATCH_IMAGE_NAME),
                'thumb' => $this->getRadioSwatchProductImage($variationProduct, self::RADIO_SWATCH_THUMBNAIL_NAME),
            ];
        }

        return $variationMediaArray;
    }


    /**
     * Retrieve collection of Radio Swatch attributes
     *
     * @param Product $product
     * @return \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute[]
     */
    private function getRadioSwatchAttributes(Product $product)
    {
        $radioSwatchAttributes = $this->_radioSwatchAttributesProvider->provide($product);
        return $radioSwatchAttributes;
    }

    /**
     * Check if the Product has Radio Swatch attributes
     *
     * @param Product $product
     * @return bool
     */
    public function isProductHasRadioSwatch(Product $product)
    {
        $swatchAttributes = $this->getRadioSwatchAttributes($product);
        return count($swatchAttributes) > 0;
    }


    /**
     * Is attribute Visual Swatch
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return bool
     */
    public function isRadioSwatch(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        if (!$attribute->hasData(AttributeTemplate::TEMPLATE_INPUT_OPTION_KEY)) {
            return false;
        } else if ($attribute->getData(AttributeTemplate::TEMPLATE_INPUT_OPTION_KEY) == AttributeTemplate::RAIDO_OPTION_ONLY) {
        	return  $attribute->getData(AttributeTemplate::TEMPLATE_INPUT_OPTION_KEY) == AttributeTemplate::RAIDO_OPTION_ONLY;
        }
        return $attribute->getData(AttributeTemplate::TEMPLATE_INPUT_OPTION_KEY) == AttributeTemplate::RADIO_WITH_SWATCH;
    }


    /**
     * @param string $attributeCode swatch_image|image
     * @param  \Magento\Catalog\Model\Product $configurableProduct
     * @param array $requiredAttributes
     * @return bool|Product
     */
    private function loadFirstVariation($attributeCode, Product $configurableProduct, array $requiredAttributes)
    {
        if ($this->isProductHasRadioSwatch($configurableProduct)) {
            $usedProducts = $configurableProduct->getTypeInstance()->getUsedProducts($configurableProduct);

            foreach ($usedProducts as $simpleProduct) {
                if (!in_array($simpleProduct->getData($attributeCode), [null, self::EMPTY_IMAGE_VALUE], true)
                    && !array_diff_assoc($requiredAttributes, $simpleProduct->getData())
                ) {
                    return $simpleProduct;
                }
            }
        }

        return false;
    }

    /**
     * @param Product $configurableProduct
     * @param array $requiredAttributes
     * @return bool|Product
     */
    public function loadFirstVariationWithImage(Product $configurableProduct, array $requiredAttributes)
    {
        return $this->loadFirstVariation('image', $configurableProduct, $requiredAttributes);
    }

    /**
     * @param \Magento\Catalog\Api\Data\ProductInterface $configurableProduct
     * @param array $requiredAttributes
     * @return bool|Product
     */
    public function loadFirstVariationWithRadioSwatchImage(\Magento\Catalog\Api\Data\ProductInterface $configurableProduct, array $requiredAttributes)
    {
        return $this->loadFirstVariation(self::RADIO_SWATCH_ATTRIBUTE_CODE, $configurableProduct, $requiredAttributes);
    }

}