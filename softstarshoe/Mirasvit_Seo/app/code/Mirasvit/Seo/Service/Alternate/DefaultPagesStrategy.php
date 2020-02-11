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



namespace Mirasvit\Seo\Service\Alternate;

class DefaultPagesStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    /**
     * @var \Mirasvit\Seo\Api\Service\Alternate\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->url = $url;
        $this->urlInterface = $urlInterface;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreUrls()
    {
        $storeUrls = $this->url->getStoresCurrentUrl();
        // To prevent "Exception #0 (Exception): Warning: Invalid argument supplied for foreach()" for some stores BEGIN
        if (!$storeUrls) {
            return false;
        }
        // To prevent "Exception #0 (Exception): Warning: Invalid argument supplied for foreach()" for some stores END
        $storeUrls = $this->getAlternateUrl($storeUrls);

        return $storeUrls;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlternateUrl($storeUrls)
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl();
        $currentUrl = $this->urlInterface->getCurrentUrl();
        $currentPath = strtok($currentUrl, '?');
        $currentPath = str_replace($baseUrl, '', $currentPath);
        $currentPath = strstr($currentPath, 'referer/', true); //prepare customer/account page
        foreach ($storeUrls as $key => $storeUrl) {
            $storeUrls[$key] =  strtok($storeUrl, '?') . $currentPath;
        }

        return $storeUrls;
    }
}