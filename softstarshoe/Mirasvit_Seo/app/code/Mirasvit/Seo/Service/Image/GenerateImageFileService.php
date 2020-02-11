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



namespace Mirasvit\Seo\Service\Image;

use Mirasvit\Seo\Api\Service\Image\GenerateImageFileServiceInterface;
use Mirasvit\Seo\Helper\Version;
use Mirasvit\Seo\Api\Config\ImageConfigServiceInterface;
use Magento\Framework\Registry;
use Mirasvit\Seo\Helper\Parse;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\App\Request\Http;

/**
 * M2.1. compatibility
 */
class GenerateImageFileService implements GenerateImageFileServiceInterface
{
    /**
     * @var array
     */
    protected $parseObjects = [];

    /**
     * @param Version $version
     * @param ImageConfigServiceInterface $imageConfig
     */
    public function __construct(
        Http $request,
        Version $version,
        ImageConfigServiceInterface $imageConfig,
        Registry $registry,
        Parse $parse,
        FilterManager $filter
    ) {
        $this->request = $request;
        $this->imageConfig = $imageConfig;
        $this->registry = $registry;
        $this->parse = $parse;
        $this->filter = $filter;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     */
    public function isUseDefaultFunctionality()
    {
        if ($this->version->getVersion() >= '2.2.0'
            || $this->version->getVersion() == '2.1.6') {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    // public function isUseDifferentFunction()
    // {
    //     if ($this->version->getVersion() == '2.1.6') {
    //         return true;
    //     }

    //     return false;
    // }

    /**
     * {@inheritdoc}
     */
    public function getFriendlyImagePath($url)
    {
        $productData = $this->registry->registry(ImageConfigServiceInterface::IMAGE_REG_DATA);
        $product = $this->parseObjects['product'] = $productData['product'];
        if (!$product || !$this->imageConfig->isEnableImageFriendlyUrl()
            || (($this->request->getFullActionName() == 'catalog_category_view'
                || $this->request->getFullActionName() == 'catalogsearch_result_index') //prevent slow page load
                && $productData['image_id'] != 'category_page_grid') ) {
                    return $url;
        }
        $imageUrlTemplate = $this->imageConfig->getImageUrlTemplate();
        $label = $this->parse->parse($imageUrlTemplate, $this->parseObjects);
        $imageName = $this->filter->translitUrl($label);
        $baseUrl = ImageConfigServiceInterface::MEDIA_PATH . '/';
        $suffix = preg_replace('/(.*)(\\.)/', '.', $url);
        $cacheUrl = preg_replace('/(.*)(cache\\/)/', '.', $url);
        $imagePath =  $product->getId() . substr(md5($productData['image_id'] . $cacheUrl), 4, 3);
        $url = $baseUrl . $imagePath . '/' . $imageName . $suffix;

        return $url;
    }
}

