<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Helper;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface as StoreManagerInterface;
use Amasty\ShopbyBase\Helper\FilterSetting as FilterSettingHelper;
use Amasty\ShopbyBase\Helper\OptionSetting as OptionSettingHelper;
use Magento\Catalog\Model\Product\Attribute\Repository as AttributeRepository;
use Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory as OptionCollectionFactory;
use Magento\Catalog\Model\Product\Url as ProductUrl;

class Data extends AbstractHelper
{
    const PATH_BRAND_URL_KEY = 'amshopby_brand/general/url_key';

    /**
     * @var UrlInterface
     */
    private $url;

    /**
     * @var OptionSettingHelper
     */
    private $optionSettingHelper;

    /**
     * @var AttributeRepository
     */
    private $attributeRepository;

    /**
     * @var OptionCollectionFactory
     */
    private $optionCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * @var ProductUrl
     */
    private $productUrl;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param OptionSettingHelper $optionSettingHelper
     * @param AttributeRepository $repository
     * @param OptionCollectionFactory $optionCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param ProductUrl $productUrl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        OptionSettingHelper $optionSettingHelper,
        AttributeRepository $repository,
        OptionCollectionFactory $optionCollectionFactory,
        StoreManagerInterface $storeManager,
        ProductUrl $productUrl
    ) {
        parent::__construct($context);
        $this->url = $context->getUrlBuilder();
        $this->optionSettingHelper = $optionSettingHelper;
        $this->attributeRepository = $repository;
        $this->optionCollectionFactory = $optionCollectionFactory;
        $this->storeManager = $storeManager;
        $this->productUrl = $productUrl;
    }
    
    public function getAllBrandsUrl($scopeCode = null)
    {
        $pageIdentifier = $this->scopeConfig->getValue(
            'amshopby_brand/general/brands_page',
            ScopeInterface::SCOPE_STORE,
            $scopeCode
        );
        return $this->url->getUrl($pageIdentifier);
    }

    /**
     * Update branded option setting collection.
     */
    public function updateBrandOptions()
    {
        $attrCode   = $this->scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
            ScopeInterface::SCOPE_STORE
        );

        if (!$attrCode) {
            return;
        }

        $filterCode = FilterSettingHelper::ATTR_PREFIX . $attrCode;
        $currentAttributeValues = $this->getCurrentBrandAttributeValues($attrCode);
        // Temporary disabled: planning feature "Relink"
        //$this->deleteExtraBrandOptions($currentAttributeValues, $filterCode);
        $this->addMissingBrandOptions($currentAttributeValues, $filterCode);
    }

    /**
     * @param string $attrCode
     * @return string[]
     */
    private function getCurrentBrandAttributeValues($attrCode)
    {
        /** @var \Magento\Eav\Model\Entity\Attribute\Option[]  $attributeOptions */
        $attributeOptions = $this->attributeRepository->get($attrCode)->getOptions();
        $attributeValues = [];
        foreach ($attributeOptions as $option) {
            if ($option->getValue()) {
                $attributeValues[] = $option->getValue();
            }
        }
        return $attributeValues;
    }

    /**
     * @param string[] $currentAttributeValues
     * @param string $filterCode
     */
    private function deleteExtraBrandOptions($currentAttributeValues, $filterCode)
    {
        $settingOptionCollection = $this->optionCollectionFactory->create();
        $optionsToDelete = $settingOptionCollection
            ->addFieldToFilter(OptionSettingInterface::FILTER_CODE, $filterCode)
            ->addFieldToFilter(OptionSettingInterface::VALUE, ['nin' => $currentAttributeValues]);
        foreach ($optionsToDelete as $optionSetting) {
            /** @var \Amasty\ShopbyBase\Model\OptionSetting $optionSetting */
            $optionSetting->getResource()->delete($optionSetting);
        }
    }

    /**
     * @param string[] $currentAttributeValues
     * @param string $filterCode
     */
    private function addMissingBrandOptions($currentAttributeValues, $filterCode)
    {
        foreach ($currentAttributeValues as $value) {
            /** @var \Amasty\ShopbyBase\Model\OptionSetting $optionSetting */
            $optionSetting = $this->optionSettingHelper->getSettingByValue($value, $filterCode, 0);
            if (!$optionSetting->getId()) {
                $optionSetting->getResource()->save($optionSetting);
            }
        }
    }

    /**
     * @return array
     */
    public function getBrandAliases()
    {
        $attributeCode = $this->getBrandAttributeCode();
        if ($attributeCode == '') {
            return [];
        }

        $suffix = '';
        if ($this->scopeConfig->isSetFlag('amasty_shopby_seo/url/add_suffix_shopby')) {
            $suffix = $this->scopeConfig
                ->getValue('catalog/seo/category_url_suffix', ScopeInterface::SCOPE_STORE);
        }

        $options = $this->attributeRepository->get($attributeCode)->getOptions();
        array_shift($options);

        $items = [];
        foreach ($options as $option) {
            $items[$option->getValue()] = $this->productUrl->formatUrlKey($option->getLabel()) . $suffix;
        }
        return $items;
    }

    /**
     * @return string
     */
    public function getBrandAttributeCode()
    {
        return $this->scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getBrandUrlKey()
    {
        return $this->scopeConfig->getValue(
            'amshopby_brand/general/url_key',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Option $option
     * @return string
     */
    public function getBrandUrl(\Magento\Eav\Model\Entity\Attribute\Option $option)
    {
        $url = '#';
        $aliases = $this->getBrandAliases();
        $urlKey = $this->scopeConfig->getValue(self::PATH_BRAND_URL_KEY, ScopeInterface::SCOPE_STORE);
        if (isset($aliases[$option->getValue()])) {
            $brandAlias = $aliases[$option->getValue()];
            $url = $this->_urlBuilder->getBaseUrl() . (!!$urlKey ? $urlKey . '/' . $brandAlias : $brandAlias);
        }

        return $url;
    }
}
