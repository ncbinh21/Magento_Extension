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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Helper;

class BlogMx extends \Magento\Framework\App\Helper\AbstractHelper
{
    const IMAGE_SIZE = 300;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\App\Filesystem\DirectoryLis
     */
    protected $directoryList;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Mirasvit\Seo\Helper\Data $seoData
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->seoData = $seoData;
        $this->directoryList = $directoryList;
    }

    /**
     * @param string &$content
     * @return bool
     */
    public function getPreparedContent(&$content)
    {
        $content = preg_replace('/\\"/', '\'', strip_tags($content));

        return true;
    }

    /**
     * @param string $date
     * @return string
     */
    public function getDatePublished($date)
    {
        return date('Y-m-d', strtotime($date));
    }

    /**
     * @return string
     */
    public function getPublisherName()
    {
        $organizationName = trim($this->scopeConfig->getValue('general/store_information/name'));

        return $organizationName ? $organizationName : 'Blog';
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        if ($logo = $this->scopeConfig->getValue('seo_snippets/blogmx_snippets/logo_file_upload')) {
            return $this->storeManager->getStore()->getBaseUrl() . 'pub/media/logo/' . $logo;
        }

        return $this->seoData->getLogoUrl();
    }

    /**
     * @param string $imageUrl
     * @return array
     */
    public function getImageSize($imageUrl)
    {
        $checkedImageUrl = [];
        $isAllowUrlFopen = ini_get('allow_url_fopen');
        $rootFolder = $this->directoryList->getRoot();
        $imageUrl = str_replace($this->storeManager->getStore()->getBaseUrl(), '/', $imageUrl);
        $imageUrlDirect = $rootFolder . $imageUrl;
        $imageUrlPub = $rootFolder . '/pub' . $imageUrl;

        if ($isAllowUrlFopen && file_exists($imageUrlDirect)) {
            $checkedImageUrl = getimagesize($imageUrlDirect);
        } elseif ($isAllowUrlFopen && file_exists($imageUrlPub)) {
            $checkedImageUrl = getimagesize($imageUrlPub);
        }

        return $checkedImageUrl;
    }

    /**
     * @param string $imageUrl
     * @return int
     */
    public function getImageWith($imageUrl)
    {
        $imageWidth = self::IMAGE_SIZE;
        $imageSize = $this->getImageSize($imageUrl);
        if (isset($imageSize[0]) && $imageSize[0]) {
            $imageWidth = $imageSize[0];
        }

        return $imageWidth;
    }

    /**
     * @param string $imageUrl
     * @return int
     */
    public function getImageHeight($imageUrl)
    {
        $imageHeight = self::IMAGE_SIZE;
        $imageSize = $this->getImageSize($imageUrl);
        if (isset($imageSize[1]) && $imageSize[1]) {
            $imageHeight = $imageSize[1];
        }

        return $imageHeight;
    }
}
