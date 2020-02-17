<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 29/08/2018
 * Time: 12:46
 */

namespace Forix\ProductWizard\Ui\DataProvider\Product\Listing\Collector;

use Magento\Catalog\Ui\DataProvider\Product\ProductRenderCollectorInterface;
use Forix\ProductWizard\Api\Data\ProductRender\AttributeOptionDataInterfaceFactory;
use \Magento\Catalog\Api\Data\ProductAttributeInterface;

class Attributes implements ProductRenderCollectorInterface
{
    protected $_wizardHelper;
    protected $_attributeRepository;
    protected $_productRenderExtensionFactory;
    protected $_attributeOptionDataFactory;
    protected $_escaper;

    public function __construct(
        \Forix\ProductWizard\Helper\Data $wizardHelper,
        \Magento\Catalog\Api\Data\ProductRenderExtensionFactory $productRenderExtensionFactory,
        AttributeOptionDataInterfaceFactory $attributeOptionDataFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Eav\Model\AttributeRepository $attributeRepository
    )
    {
        $this->_escaper = $escaper;
        $this->_wizardHelper = $wizardHelper;
        $this->_attributeOptionDataFactory = $attributeOptionDataFactory;
        $this->_productRenderExtensionFactory = $productRenderExtensionFactory;
        $this->_attributeRepository = $attributeRepository;
    }

    /**
     * Get Configurable Attribute Data
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $_product
     * @param string[] $attributeCodes
     * @return \Forix\ProductWizard\Api\Data\ProductRender\AttributeOptionDataInterface[]
     */
    private function getAttributesData($_product, $attributeCodes)
    {
        $attributesData = [];
        $skuData = $this->_attributeOptionDataFactory->create();
        $skuData->setAttributeLabel(__("Part #"));
        $skuData->setAttributeCode("sku");
        $skuData->setAttributeValues($_product->getSku());
        foreach ($attributeCodes as $attributeCode) {
            if($attributeCode) {
                $attribute = $this->_attributeRepository->get(ProductAttributeInterface::ENTITY_TYPE_CODE, $attributeCode);
                if($attribute) {
                    if ($_product->getData($attribute->getAttributeCode())) {
                        $values = $_product->getAttributeText($attribute->getAttributeCode());
                        if (!is_array($values)) {
                            $values = [$values];
                        }
                        $attributeData = $this->_attributeOptionDataFactory->create();
                        $attributeData->setAttributeCode($attribute->getAttributeCode());
                        $attributeData->setAttributeLabel($attribute->getStoreLabel());
                        $attributeData->setAttributeValues(implode(',', $values));
                        $attributesData[] = $attributeData;
                    }
                }
            }
        }

        return $attributesData;
    }

    /**
     * Takes information from Product, map to render information and hydrate render object
     *
     * @param \Magento\Catalog\Api\Data\ProductInterface $product
     * @param \Magento\Catalog\Api\Data\ProductRenderInterface $productRender
     * @param array $data
     * @return void
     * @since 101.1.0
     */
    public function collect(\Magento\Catalog\Api\Data\ProductInterface $product, \Magento\Catalog\Api\Data\ProductRenderInterface $productRender)
    {
        $extensionAttributes = $productRender->getExtensionAttributes();
        if (null === $extensionAttributes) {
            $extensionAttributes = $this->_productRenderExtensionFactory->create();
        }
        if ($attributeCodes = $this->_wizardHelper->getConfigAttributes($product->getAttributeSetId())) {
            if (count($attributeCodes)) {
                /**
                 * @var $attributeData \Forix\ProductWizard\Api\Data\ProductRender\AttributeOptionDataInterface
                 */
                $attributeOptionData = $this->getAttributesData($product, $attributeCodes);
                $extensionAttributes->setAttributeOptionData($attributeOptionData);
                $productRender->setExtensionAttributes($extensionAttributes);
            }
        }
    }
}