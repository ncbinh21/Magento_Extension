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



namespace Mirasvit\Seo\Plugin\Image;

use Mirasvit\Seo\Api\Config\ImageConfigServiceInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Catalog\Model\Product\Media\ConfigInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Registry;
use Magento\Framework\App\State;

class ImageBasePathPlugin
{
    /**
     * @var array
     */
    protected $isChangePath = [];

    /**
     * ImageBasePathPlugin constructor.
     * @param ImageConfigServiceInterface $imageConfig
     * @param StoreManagerInterface $storeManager
     * @param ConfigInterface $mediaConfig
     * @param Filesystem $filesystem
     * @param Registry $registry
     * @param State $state
     */
    public function __construct(
        ImageConfigServiceInterface $imageConfig,
        StoreManagerInterface $storeManager,
        ConfigInterface $mediaConfig,
        Filesystem $filesystem,
        Registry $registry,
        State $state
    ) {
        $this->imageConfig = $imageConfig;
        $this->storeManager = $storeManager;
        $this->mediaConfig = $mediaConfig;
        $this->filesystem = $filesystem;
        $this->mediaDirectory = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mediaDirectory->create($this->mediaConfig->getBaseMediaPath());
        $this->registry = $registry;
        $this->state = $state;
    }

    /**
     * @param \Magento\Catalog\Model\View\Asset\Image\Context $subject
     * @param string $path
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPath($subject, $path)
    {
        if ($this->imageConfig->isEnableImageFriendlyUrl()
            && ($isChangePath = $this->isChangePath())) {
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
        if ($this->imageConfig->isEnableImageFriendlyUrl()
            && ($isChangePath = $this->isChangePath())) {
                return $this->storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
                    . ImageConfigServiceInterface::MEDIA_PATH;
        }

        return $baseUrl;
    }

    /**
     * @return bool
     */
    protected function isChangePath()
    {
        if ($this->state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML) {
            return true;
        }
        if (!$this->isProductExist()) {
            return false;
        }
        $thumbnail = '';
        $image = '';
        $smallImage = '';
        $isChangePath = true;

        $product = $this->registry->registry(ImageConfigServiceInterface::IMAGE_REG_DATA)['product'];

        if (isset($this->isChangePath[$product->getId()])) {
            return $this->isChangePath[$product->getId()];
        }
        if ($product) {
            $thumbnail = $product->getData('thumbnail');
            $image = $product->getData('image');
            $smallImage = $product->getData('small_image');
        }
        if ((!$thumbnail || $thumbnail == 'no_selection')
            && (!$image || $image == 'no_selection')
            && (!$smallImage || $smallImage == 'no_selection')
        ) {
            $isChangePath = false;
        }

        $this->isChangePath[$product->getId()] = $isChangePath;

        return $isChangePath;
    }

    /**
     * @return bool
     */
    protected function isProductExist()
    {
        $imageRegData = $this->registry->registry(ImageConfigServiceInterface::IMAGE_REG_DATA);
        if (is_array($imageRegData)
            && isset($imageRegData['product'])
            && ($imageRegData['product'])
            && is_object($imageRegData['product'])) {
            return true;
        }

        return false;
    }
}