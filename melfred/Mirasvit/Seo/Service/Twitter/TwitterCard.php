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



namespace Mirasvit\Seo\Service\Twitter;

use Mirasvit\Seo\Api\Service\Twitter\TwitterCardInterface as TwitterCardInterface;
use Mirasvit\Seo\Model\Config as Config;
use Mirasvit\Seo\Api\Config\CurrentPageProductsInterface;


class TwitterCard implements TwitterCardInterface
{
    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $catalogImage;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var string
     */
    protected $_cardType;

    /**
     * @var string
     */
    protected $tags;

    /**
     * @var null|bool
     */
    protected $isTwitterCardAdded;

    /**
     * @var
     */

    /**
     * @param \Mirasvit\Seo\Model\Config                 $config
     * @param \Magento\Catalog\Helper\Image              $catalogImage
     * @param \Mirasvit\Seo\Helper\Data                  $seoData
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Registry                $registry
     */
    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Catalog\Helper\Image $catalogImage,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->config = $config;
        $this->seoData = $seoData;
        $this->catalogImage = $catalogImage;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
    }

    /**
     * @param string $body
     * @param string $fullActionCode
      @param bool|Magento\Framework\App\Response\Http $response
     * @return string
     */
    public function addTwitterCard($body, $fullActionCode,  $response = false)
    {
        $applyForCache = ($response) ? true : false;
        $body = ($response) ? $response->getBody() : $body;
        $fullActionCode = ($fullActionCode) ? : $this->seoData->getFullActionCode();

        $this->setTwitterCardType();

        if (!$this->_cardType || $this->isTwitterCardAdded) {
            return ($applyForCache) ? $response : $body;
        }

        $this->seo = $this->seoData->getCurrentSeo();
        $this->tags = $this->createMetaTag('card', $this->_cardType);

        $this->setSiteTag();

        preg_match("/<title>(.*)<\/title>/siU", $body, $titleTag);
        isset($titleTag[1]) ? $this->setTitleTag($titleTag[1]) :
                              $this->setTitleTag() ;

        preg_match('/<meta.*?name=("|\')description("|\').*?content=("|\')(.*?)("|\')/i', $body, $metaDescription);
        isset($metaDescription[4]) ? $this->setDescriptionTag($metaDescription[4]) :
                                     $this->setDescriptionTag() ;

        $this->setImageTags($fullActionCode);

        $this->tags = "\n".'<!-- mirasvit twitter card begin -->'.
                      "\n" . $this->tags . '<!-- mirasvit twitter card end -->';
        $body = str_replace('</title>', '</title>'.$this->tags, $body);

        $this->isTwitterCardAdded = true;

        if ($applyForCache) {
            $response->setBody($body);
            return $response;
        }

        return $body;

    }

    /**
     * @return void
     */
    public function setTwitterCardType()
    {
        switch ($this->config->getTwitterCard($this->storeManager->getStore()->getId()))
        {
            case TwitterCardInterface::TWITTERCARD_SMALL_IMAGE:
                $this->_cardType = "summary";
                break;
            case TwitterCardInterface::TWITTERCARD_LARGE_IMAGE:
                $this->_cardType = "summary_large_image";
                break;
            default:
                $this->_cardType = '';
                break;
        }
    }

    /**
     * @return void
     */
    public function setSiteTag()
    {
        if ($user = $this->config->getTwitterUser()) {
            if (strpos($user, '@') !== 0) {
                $user = '@'.$user;
            }
            $this->tags .= $this->createMetaTag('site', $user);
        }
    }

    /**
     * @param string $titleTag
     * @return void
     */
    public function setTitleTag($titleTag = '')
    {
        $metaTitle = ($titleTag
            && !$this->config->isUseHtmlSymbolsInMetaTags()
            && $this->seo->getMetaTitle())
            ? $titleTag : $this->seo->getMetaTitle();
        $this->tags .= $this->createMetaTag('title', $metaTitle);
    }

    /**
     * @param string $metaDescription
     * @return void
     */
    public function setDescriptionTag($metaDescription = '')
    {
        $description = ($metaDescription
            && !$this->config->isUseHtmlSymbolsInMetaTags()
            && $this->seo->getMetaDescription())
            ? $metaDescription : str_replace('"', '', $this->seo->getMetaDescription()) ;
        $this->tags .= $this->createMetaTag('description', $description);
    }

    /**
     * @param string $fullActionCode
     * @return void
     */
    public function setImageTags($fullActionCode)
    {
        switch ($fullActionCode) {
            case 'catalog_product_view':
                $product = $this->registry->registry('current_product');
                if ($product && $product->getData('image') != 'no_selection') {
                    // Twitter resizes card image automatically, quality of "medium" image is optimal to serve
                    $mediumImageUrl = $this->catalogImage
                                           ->init($product, 'product_page_image_medium')
                                           ->getUrl();
                    $this->tags .= $this->createMetaTag('image', $mediumImageUrl);
                    $this->tags .= $this->createMetaTag('image:alt', $this->catalogImage->getLabel());
                }
                break;

            case 'catalog_category_view':
                if ($productCollection = $this->registry->registry(CurrentPageProductsInterface::PRODUCT_COLLECTION)) {
                    if ($productCollection->count()) {
                        $baseImageUrl = $this->catalogImage
                                             ->init($productCollection->getFirstItem(), 'product_page_image_medium')
                                             ->getUrl();
                        $this->tags .= $this->createMetaTag('image', $baseImageUrl);
                        $this->tags .= $this->createMetaTag('image:alt', $this->catalogImage->getLabel());
                    }
                }
                break;

            default:
                if ($logoUrl = $this->seoData->getLogoUrl()) {
                    $this->tags .= $this->createMetaTag('image', $logoUrl);
                }
                if ($logoAlt = $this->seoData->getLogoAlt()) {
                    $this->tags .= $this->createMetaTag('image:alt', $logoAlt);
                }

                break;
        }
    }

    /**
     * @param string $property
     * @param string $value
     * @return string
     */
    protected function createMetaTag($property, $value)
    {
        $value = $this->seoData->cleanMetaTag($value);

        return "<meta name=\"twitter:$property\" content=\"$value\"/>"."\n";
    }
}