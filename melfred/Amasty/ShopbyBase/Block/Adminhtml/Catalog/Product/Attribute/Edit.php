<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Block\Adminhtml\Catalog\Product\Attribute;

/**
 * Class Edit
 * @package Amasty\ShopbyBase\Block\Adminhtml\Catalog\Product\Attribute
 */
class Edit extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var \Amasty\ShopbyBase\Model\Source\DisplayMode\Proxy
     */
    private $displayModeSource;

    /**
     * @var \Amasty\ShopbyBase\Model\FilterSetting\AttributeConfig
     */
    private $attributeSettingsConfig;

    /**
     * Edit constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Amasty\ShopbyBase\Model\Source\DisplayMode\Proxy $displayModeSource
     * @param \Amasty\ShopbyBase\Model\FilterSetting\AttributeConfig $attributeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\ShopbyBase\Model\Source\DisplayMode\Proxy $displayModeSource,
        \Amasty\ShopbyBase\Model\FilterSetting\AttributeConfig $attributeConfig,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->displayModeSource = $displayModeSource;
        $this->attributeSettingsConfig = $attributeConfig;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getFilterCode()
    {
        /** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $attribute = $this->coreRegistry->registry('entity_attribute');

        return \Amasty\ShopbyBase\Helper\FilterSetting::ATTR_PREFIX . $attribute->getAttributeCode();
    }

    /**
     * @return bool
     */
    public function canConfigureAttributeOptions()
    {
        $attribute = $this->coreRegistry->registry('entity_attribute');
        return $this->attributeSettingsConfig->canBeConfigured($attribute->getAttributeCode());
    }
}
