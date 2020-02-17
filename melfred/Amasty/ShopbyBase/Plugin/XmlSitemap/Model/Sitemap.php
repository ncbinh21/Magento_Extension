<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Plugin\XmlSitemap\Model;

use Amasty\XmlSitemap\Model\Sitemap as NativeSitemap;
use Magento\Framework\DataObjectFactory as ObjectFactory;

class Sitemap
{
    /**
     * @var ObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var \Amasty\ShopbyBase\Helper\OptionSetting
     */
    private $optionSetting;

    /**
     * @var \Amasty\ShopbyBase\Model\XmlSitemap
     */
    private $xmlSitemap;

    public function __construct(
        ObjectFactory $dataObjectFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Eav\Model\Config $eavConfig,
        \Amasty\ShopbyBase\Helper\OptionSetting $optionSetting,
        \Amasty\ShopbyBase\Model\XmlSitemap $xmlSitemap
    ) {

        $this->dataObjectFactory = $dataObjectFactory;
        $this->scopeConfig = $scopeConfig;
        $this->eavConfig = $eavConfig;
        $this->optionSetting = $optionSetting;
        $this->xmlSitemap = $xmlSitemap;
    }

    public function aroundGetBrandCollection(NativeSitemap $subgect, \Closure $proceed, $storeId)
    {
        return $this->xmlSitemap->getBrandUrls($storeId);
    }
}
