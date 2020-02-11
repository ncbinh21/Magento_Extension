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
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Mirasvit\Seo\Helper\Parse;
use Magento\Framework\Filter\FilterManager;

class ImagePathPlugin
{
    /**
     * @var array
     */
    protected $parseObjects = [];

    /**
     * @param ImageConfigServiceInterface $imageConfig
     */
    public function __construct(
        ImageConfigServiceInterface $imageConfig,
        StoreManagerInterface $storeManager,
        Registry $registry,
        Parse $parse,
        FilterManager $filter
    ) {
        $this->imageConfig = $imageConfig;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->parse = $parse;
        $this->filter = $filter;
    }

    /**
     * @param \Magento\Catalog\Model\View\Asset\Image $subject
     * @param string $url
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetUrl($subject, $url)
    {
        if ($this->imageConfig->isEnableImageFriendlyUrl()
            && ($productData = $this->registry->registry(ImageConfigServiceInterface::IMAGE_REG_DATA))) {
                $url = $this->getFriendlyImagePath($productData, $url);
        }

        return $url;
    }

    /**
     * @param \Magento\Catalog\Model\View\Asset\Image $subject
     * @param string $path
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPath($subject, $path)
    {
        if ($this->imageConfig->isEnableImageFriendlyUrl()
            && ($productData = $this->registry->registry(ImageConfigServiceInterface::IMAGE_REG_DATA))) {
                $path = $this->getFriendlyImagePath($productData, $path);
        }

        return $path;
    }

    /**
     * @param \Magento\Catalog\Model\Product $productData
     * @param string $url
     * @return string
     */
    private function getFriendlyImagePath($productData, $url)
    {
        $imageUrlTemplate = $this->imageConfig->getImageUrlTemplate();
        $product = $this->parseObjects['product'] = $productData['product'];
        $label = $this->parse->parse($imageUrlTemplate, $this->parseObjects);
        $imageName = $this->filter->translitUrl($label);
        $baseUrl = preg_replace('/(cache\\/)(.*)/', '', $url);
        $suffix = preg_replace('/(.*)(\\.)/', '.', $url);
        $cacheUrl = preg_replace('/(.*)(cache\\/)/', '.', $url);
        $imagePath =  $product->getId() . substr(md5($productData['image_id'] . $cacheUrl), 4, 3);
        $url = $baseUrl . $imagePath . '/' . $imageName . $suffix;

        return $url;
    }
}