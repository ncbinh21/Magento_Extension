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



namespace Mirasvit\Seo\Plugin\Image;

use Mirasvit\Seo\Api\Config\ImageConfigServiceInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\Product\Media\ConfigInterface;
use Magento\Framework\Filesystem;

class ImageBasePathPlugin
{
    /**
     * @param ImageConfigServiceInterface $imageConfig
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $mediaConfig
     * @param Filesystem $filesystem
     */
    public function __construct(
        ImageConfigServiceInterface $imageConfig,
        StoreManagerInterface $storeManager,
        ConfigInterface $mediaConfig,
        Filesystem $filesystem
    ) {
        $this->imageConfig = $imageConfig;
        $this->storeManager = $storeManager;
        $this->mediaConfig = $mediaConfig;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mediaDirectory->create($this->mediaConfig->getBaseMediaPath());
    }

    /**
     * @param \Magento\Catalog\Model\View\Asset\Image\Context $subject
     * @param string $path
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPath($subject, $path)
    {
        if ($this->imageConfig->isEnableImageFriendlyUrl()) {
            $path = $this->mediaDirectory->getAbsolutePath(ImageConfigServiceInterface::MEDIA_PATH);
        }

        return $path;
    }

    /**
     * @param \Magento\Catalog\Model\View\Asset\Image\Context $subject
     * @param string $baseUrl
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetBaseUrl($subject, $baseUrl)
    {
        if ($this->imageConfig->isEnableImageFriendlyUrl()) {
            return $this->storeManager->getStore()
                ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                . ImageConfigServiceInterface::MEDIA_PATH;
        }

        return $baseUrl;
    }
}