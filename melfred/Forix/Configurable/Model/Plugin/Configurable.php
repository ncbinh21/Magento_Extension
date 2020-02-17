<?php

namespace Forix\Configurable\Model\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable\Product\Collection;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory;

use Forix\Configurable\Helper\Data;
use \Forix\Configurable\Helper\RadioSwatchHelper;

class Configurable
{
    protected $helper;
    protected $attributeHelper;

    /**
     * Configurable constructor.
     * @param Data $helper
     */
    public function __construct(
        Data $helper,
        RadioSwatchHelper $attributeHelper
    )
    {
        $this->helper = $helper;
        $this->attributeHelper = $attributeHelper;
    }


    /**
     * Add swatch attributes to Configurable Products Collection
     *
     * @param ConfigurableProductType $subject
     * @param Collection $result
     * @param ProductInterface $product
     * @return Collection
     */
    public function afterGetUsedProductCollection(
        ConfigurableProductType $subject,
        Collection $result,
        ProductInterface $product
    )
    {
        $fitmenAttr = $this->helper->getFitMenAttribute();

        $result->addAttributeToSelect('mb_rig_model');
        $result->addAttributeToSelect(array_keys($fitmenAttr));

        $radioSwatchAttributes = ['mb_option_image'];
        foreach ($subject->getUsedProductAttributes($product) as $code => $attribute) {
            if ($this->attributeHelper->isRadioSwatch($attribute)) {
                $radioSwatchAttributes[] = $attribute->getAttributeCode();
            }
        }
        $result->addAttributeToSelect($radioSwatchAttributes);
        return $result;
    }
}
