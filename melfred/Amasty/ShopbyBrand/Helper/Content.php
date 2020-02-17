<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Helper;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Amasty\ShopbyBase\Helper\OptionSetting;
use Magento\Catalog\Model\Layer;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManager;

class Content extends AbstractHelper
{
    const APPLIED_BRAND_VALUE = 'applied_brand_customizer_value';
    const CATEGORY_FORCE_MIXED_MODE = 'amshopby_force_mixed_mode';
    const CATEGORY_SHOPBY_IMAGE_URL = 'amshopby_category_image_url';

    /**
     * @var  Layer\Resolver
     */
    private $layerResolver;

    /**
     * @var  OptionSetting
     */
    private $optionHelper;

    /**
     * @var  StoreManager
     */
    private $storeManager;

    /** ShopbyBase\Api\Data\OptionSettingInterface|bool */
    private $currentBranding = false;

    public function __construct(
        Context $context,
        Layer\Resolver $layerResolver,
        OptionSetting $optionHelper,
        StoreManager $storeManager
    ) {
        parent::__construct($context);
        $this->layerResolver = $layerResolver;
        $this->optionHelper = $optionHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * Get current Brand.
     * @return null|OptionSettingInterface
     */
    public function getCurrentBranding()
    {
        if ($this->currentBranding === false) {
            if ($this->_getRequest()->getControllerName() !== 'index') {
                return $this->currentBranding = null;
            }

            $attributeCode =
                $this->scopeConfig->getValue('amshopby_brand/general/attribute_code', ScopeInterface::SCOPE_STORE);
            if ($attributeCode == '') {
                return $this->currentBranding = null;
            }

            $value = $this->_request->getParam($attributeCode);

            if (!isset($value)) {
                return $this->currentBranding = null;
            }

            $layer = $this->layerResolver->get();
            $isRootCategory = $layer->getCurrentCategory()->getId() == $layer->getCurrentStore()->getRootCategoryId();
            if (!$isRootCategory) {
                return $this->currentBranding = null;
            }

            $this->currentBranding = $this->optionHelper->getSettingByValue(
                $value,
                \Amasty\ShopbyBase\Helper\FilterSetting::ATTR_PREFIX . $attributeCode,
                $this->storeManager->getStore()->getId()
            );
        }

        return $this->currentBranding;
    }
}
