<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Plugin\Catalog\Block\Product\View;

use Amasty\ShopbyBase\Helper\FilterSetting as FilterHelper;
use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\ShopbyBase\Model\OptionSetting;
use Amasty\ShopbyBase\Model\FilterSetting;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

abstract class BlockHtmlTitlePluginAbstract
{
    const IMAGE_URL     = 'image_url';
    const LINK_URL      = 'link_url';
    const TITLE         = 'title';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var \Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\Collection
     */
    protected $filterCollection;

    /**
     * @var \Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\Collection
     */
    protected $optionCollection;

    /**
     * @var Configurable
     */
    protected $configurableType;

    /**
     * @var FilterSetting\AttributeConfig
     */
    protected $attributeConfig;

    /**
     * @var \Amasty\ShopbyBase\Helper\Data
     */
    protected $baseHelper;

    /**
     * BlockHtmlTitlePlugin constructor.
     * @param \Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory $filterCollectionFactory
     * @param \Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory $optionCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Configurable $configurableType
     */
    public function __construct(
        \Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory $filterCollectionFactory,
        \Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory $optionCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Configurable $configurableType,
        \Amasty\ShopbyBase\Model\FilterSetting\AttributeConfig $attributeConfig,
        \Amasty\ShopbyBase\Helper\Data $baseHelper
    ) {
        $this->filterCollection = $filterCollectionFactory->create();
        $this->optionCollection = $optionCollectionFactory->create();
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->blockFactory = $blockFactory;
        $this->configurableType = $configurableType;
        $this->attributeConfig = $attributeConfig;
        $this->baseHelper = $baseHelper;
    }

    /**
     * Add Brand Label to Product Page
     *
     * @param \Magento\Theme\Block\Html\Title $original
     * @param $html
     * @return string
     */
    public function afterToHtml(
        \Magento\Theme\Block\Html\Title $original,
        $html
    ) {
        $optionsData = $this->getOptionsData();

        if (!count($optionsData)) {
            return $html;
        }

        $block = $this->blockFactory->createBlock(\Magento\Framework\View\Element\Template::class)
            ->setData('options_data', $optionsData)
            ->setTemplate('Amasty_ShopbyBase::attribute/icon.phtml');

        $count = 1;
        $html = str_replace('/h1>', '/h1>' . $block->toHtml(), $html, $count);

        return $html;
    }

    /**
     * @return array
     */
    abstract protected function getAttributeCodes();

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    protected function getAttributeValues(\Magento\Catalog\Model\Product $product)
    {
        $values = [];
        $attributeCodes = $this->getAttributeCodes();
        if (!count($attributeCodes)) {
            return $values;
        }

        foreach ($attributeCodes as $code) {
            $data = $product->getData($code);
            if (!$data && $product->getTypeId() === Configurable::TYPE_CODE) {
                /** @var \Magento\Catalog\Model\Product[] $childProducts */
                $childProducts = $this->configurableType->getUsedProducts($product);
                foreach ($childProducts as $childProduct) {
                    if ($childProduct->getData($code)) {
                        $values = array_merge($values, explode(',', $childProduct->getData($code)));
                    }
                }
            } elseif ($data) {
                $values = array_merge($values, explode(',', $data));
            }
        }

        return $values;
    }

    /**
     * @return array
     */
    protected function getOptionsData()
    {
        $data = [];
        $product = $this->registry->registry('current_product');
        if (!$product) {
            return $data;
        }

        $attributeValues = $this->getAttributeValues($product);
        if (!count($attributeValues)) {
            return $data;
        }

        $settingCollection = $this->optionCollection
            ->addFieldToSelect(OptionSetting::TITLE)
            ->addFieldToSelect(OptionSetting::VALUE)
            ->addFieldToSelect(OptionSetting::SLIDER_IMAGE)
            ->addFieldToSelect(OptionSetting::IMAGE)
            ->addFieldToSelect(OptionSetting::FILTER_CODE)
            ->addFieldToFilter(OptionSetting::VALUE, ['in' => $attributeValues])
            ->addFieldToFilter(
                [OptionSetting::SLIDER_IMAGE, OptionSetting::IMAGE],
                [['neq' => ''],['neq' => '']]
            );

        //default_store options will be rewritten with current_store ones.
        $settingCollection->getSelect()->order(['filter_code ASC', 'store_id ASC']);

        foreach ($settingCollection as $setting) {
            /** @var OptionSetting $setting */
            $data = array_merge($data, $this->getSettingData($setting));
        }

        return $data;
    }

    /**
     * @param $setting
     * @return array
     */
    protected function getSettingData($setting)
    {
        $data = [];
        $data[$setting->getValue()][self::IMAGE_URL] = $setting->getSliderImageUrl();
        $data[$setting->getValue()][self::LINK_URL] = $setting->getUrlPath();
        $data[$setting->getValue()][self::TITLE] = $setting->getTitle();
        return $data;
    }
}
