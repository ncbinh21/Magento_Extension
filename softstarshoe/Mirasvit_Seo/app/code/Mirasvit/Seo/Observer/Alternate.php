<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Seo\Api\Config\AlternateConfigInterface as AlternateConfig;
use Magento\Directory\Helper\Data as Data;

class Alternate implements ObserverInterface
{
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Mirasvit\Seo\Api\Config\AlternateConfigInterface
     */
    protected $alternateConfig;

    /**
     * @var \Mirasvit\Seo\Helper\CheckPage
     */
    protected $checkPage;

    /**
     * @var \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
     */
    protected $strategy;

    /**
     * @var \Mirasvit\Seo\Api\Service\Alternate\UrlInterface
     */
    protected $url;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig
     * @param \Mirasvit\Seo\Helper\CheckPage $checkPage
     * @param \Mirasvit\Seo\Service\Alternate\StrategyFactory $strategyFactory
     * @param \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig,
        \Mirasvit\Seo\Helper\CheckPage $checkPage,
        \Mirasvit\Seo\Service\Alternate\StrategyFactory $strategyFactory,
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
    ) {
        $this->context = $context;
        $this->request = $context->getRequest();
        $this->alternateConfig = $alternateConfig;
        $this->checkPage = $checkPage;
        $this->strategy = $strategyFactory->create();
        $this->url = $url;
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function setupAlternateTag()
    {
        if (!$this->alternateConfig->getAlternateHreflang($this->context
                ->getStoreManager()
                ->getStore()
                ->getStoreId()) || !$this->request) {
            return false;
        }

        if ($this->checkPage->isFilterPage()) {
            return false;
        }

        $storeUrls = $this->strategy->getStoreUrls();

        $this->addLinkAlternate($storeUrls);
    }

    /**
     * Create alternate.
     *
     * @param array $storeUrls
     * @return bool
     */
    public function addLinkAlternate($storeUrls)
    {
        if (!$storeUrls) {
            return false;
        }
        $pageConfig = $this->context->getPageConfig();
        $type = 'alternate';
        $addLocaleCodeAutomatical = $this->alternateConfig->isHreflangLocaleCodeAddAutomatical();
        foreach ($storeUrls as $storeId => $url) {
            $hreflang = false;
            $storeCode = $this->url->getStores()[$storeId]->getConfig(Data::XML_PATH_DEFAULT_LOCALE);
            if ($this->alternateConfig->getAlternateHreflang($storeId) == AlternateConfig::ALTERNATE_CONFIGURABLE) {
                $hreflang = $this->alternateConfig->getAlternateManualConfig($storeId, true);
            }

            if (!$hreflang) {
                $hreflang = ($hreflang = $this->alternateConfig->getHreflangLocaleCode($storeId)) ?
                    substr($storeCode, 0, 2) . '-' . strtoupper($hreflang) :
                    (($addLocaleCodeAutomatical) ? str_replace('_', '-', $storeCode) :
                        substr($storeCode, 0, 2));
            }

            $pageConfig->addRemotePageAsset(
                html_entity_decode($url),
                $type,
                ['attributes' => ['rel' => $type, 'hreflang' => $hreflang]]
            );
        }

        $this->addXDefault($storeUrls, $type, $pageConfig);

        return true;
    }

    /**
     * Create x-default
     *
     * @param array $storeUrls
     * @param string $type
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @return bool
     */
    public function addXDefault($storeUrls, $type, $pageConfig)
    {
        $xDefaultUrl = false;
        $store = $this->context->getStoreManager()->getStore();
        if ($this->alternateConfig->getAlternateHreflang($store) == AlternateConfig::ALTERNATE_CONFIGURABLE) {
            $xDefaultUrl = $this->alternateConfig->getAlternateManualXDefault($storeUrls);
        } elseif ($this->alternateConfig->getXDefault() == AlternateConfig::X_DEFAULT_AUTOMATICALLY) {
            reset($storeUrls);
            $storeIdXDefault = key($storeUrls);
            $xDefaultUrl = $storeUrls[$storeIdXDefault];
        } elseif ($this->alternateConfig->getXDefault()) {
            $xDefaultUrl = $this->alternateConfig->getXDefault();
        }

        if ($xDefaultUrl) {
            $pageConfig->addRemotePageAsset(
                html_entity_decode($xDefaultUrl),
                $type,
                ['attributes' => ['rel' => $type, 'hreflang' => 'x-default']]
            );
        }

        return true;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->setupAlternateTag();
    }
}
