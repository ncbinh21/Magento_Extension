<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Plugin\Catalog\Block\Product\View;

use Amasty\ShopbyBase\Helper\FilterSetting as FilterHelper;
use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\ShopbyBase\Model\OptionSetting;
use Amasty\ShopbyBase\Model\FilterSetting;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Amasty\ShopbyBase\Plugin\Catalog\Block\Product\View\BlockHtmlTitlePluginAbstract;

class BlockHtmlTitlePlugin extends BlockHtmlTitlePluginAbstract
{

    /**
     * @var \Amasty\ShopbyBase\Helper\Data
     */
    protected $brandHelper;

    /**
     * BlockHtmlTitlePlugin constructor.
     * @param \Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory $filterCollectionFactory
     * @param \Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory $optionCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\View\Element\BlockFactory $blockFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Configurable $configurableType
     * @param FilterSetting\AttributeConfig $attributeConfig
     * @param \Amasty\ShopbyBase\Helper\Data $baseHelper
     * @param \Amasty\ShopbyBrand\Helper\Data $brandHelper
     */
    public function __construct(
        \Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory $filterCollectionFactory,
        \Amasty\ShopbyBase\Model\ResourceModel\OptionSetting\CollectionFactory $optionCollectionFactory,
        \Magento\Framework\Registry $registry, \Magento\Framework\View\Element\BlockFactory $blockFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Configurable $configurableType,
        FilterSetting\AttributeConfig $attributeConfig,
        \Amasty\ShopbyBase\Helper\Data $baseHelper,
        \Amasty\ShopbyBrand\Helper\Data $brandHelper
    ) {
        parent::__construct(
            $filterCollectionFactory,
            $optionCollectionFactory,
            $registry,
            $blockFactory,
            $storeManager,
            $configurableType,
            $attributeConfig,
            $baseHelper
        );
        $this->brandHelper = $brandHelper;
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
        if (!$this->baseHelper->isShopbyInstalled()) {
            return parent::afterToHtml($original, $html);
        }
        return $html;
    }

    /**
     * @return array
     */
    protected function getAttributeCodes()
    {
        return $this->brandHelper->getBrandAttributeCode() ? [$this->brandHelper->getBrandAttributeCode()] : [];
    }


    /**
     * @param $setting
     * @return array
     */
    protected function getSettingData($setting)
    {
        $data = [];
        $data[$setting->getValue()][self::IMAGE_URL] = $setting->getSliderImageUrl();
        $data[$setting->getValue()][self::LINK_URL] = $this->brandHelper->getBrandUrl($setting->getAttributeOption());
        $data[$setting->getValue()][self::TITLE] = $setting->getTitle();
        return $data;
    }
}
