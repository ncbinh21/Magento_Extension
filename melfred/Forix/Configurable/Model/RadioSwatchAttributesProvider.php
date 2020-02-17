<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/08/2018
 * Time: 10:41
 */

namespace Forix\Configurable\Model;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use \Magento\Catalog\Api\Data\ProductInterface as Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute;

/**
 * Provide list of swatch attributes for product.
 */
class RadioSwatchAttributesProvider
{
    /**
     * @var Configurable
     */
    private $typeConfigurable;

    /**
     * @var RadioSwatchAttributeCodes
     */
    private $radioSwatchAttributeCodes;

    /**
     * Key is productId, value is list of attributes
     * @var Attribute[]
     */
    private $attributesPerProduct;

    /**
     * @param Configurable $typeConfigurable
     * @param RadioSwatchAttributeCodes $radioSwatchAttributeCodes
     */
    public function __construct(
        Configurable $typeConfigurable,
        RadioSwatchAttributeCodes $radioSwatchAttributeCodes
    )
    {
        $this->typeConfigurable = $typeConfigurable;
        $this->radioSwatchAttributeCodes = $radioSwatchAttributeCodes;
    }

    /**
     * Provide list of swatch attributes for product. If product is not configurable return empty array
     * Key is productId, value is list of attributes
     *
     * @param Product $product
     * @return Attribute[]
     */
    public function provide(Product $product)
    {
        if ($product->getTypeId() !== Configurable::TYPE_CODE) {
            return [];
        }
        if (!isset($this->attributesPerProduct[$product->getId()])) {
            $configurableAttributes = $this->typeConfigurable->getConfigurableAttributes($product);
            $radioSwatchAttributeCodeMap = $this->radioSwatchAttributeCodes->getCodes();
            $radioWwatchAttributes = [];
            foreach ($configurableAttributes as $configurableAttribute) {
                if (array_key_exists($configurableAttribute->getAttributeId(), $radioSwatchAttributeCodeMap)) {
                    $radioWwatchAttributes[$configurableAttribute->getAttributeId()]
                        = $configurableAttribute->getProductAttribute();
                }
            }
            $this->attributesPerProduct[$product->getId()] = $radioWwatchAttributes;
        }
        return $this->attributesPerProduct[$product->getId()];
    }
}
