<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Model\Customizer\Category;

use Amasty\ShopbyBase\Api\CategoryDataSetterInterface;
use Amasty\ShopbyBase\Model\Customizer\Category\CustomizerInterface;
use Magento\Catalog\Model\Category;
use Amasty\ShopbyBase\Model\Category\Manager as CategoryManager;

class Brand implements CustomizerInterface
{
    const APPLY_TO_HEADING = 'am_apply_to_heading';
    const APPLY_TO_META = 'am_apply_to_meta';
    const HEADING_TITLE_SEPARATOR = '-';
    const HEADING_DESCRIPTION_SEPARATOR = ',';

    /**
     * @var \Amasty\ShopbyBrand\Helper\Content
     */
    private $brandContentHelper;

    /**
     * @var  Category
     */
    private $category;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    private $layer;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Amasty\ShopbyBase\Helper\OptionSetting
     */
    private $optionSettingHelper;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    public function __construct(
        \Amasty\ShopbyBrand\Helper\Content $brandContentHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\App\RequestInterface $request,
        \Amasty\ShopbyBase\Helper\OptionSetting $optionSettingHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->brandContentHelper = $brandContentHelper;
        $this->layer = $layerResolver->get();
        $this->request = $request;
        $this->optionSettingHelper = $optionSettingHelper;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->pageConfig = $pageConfig;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function prepareData(Category $category)
    {
        $brand = $this->brandContentHelper->getCurrentBranding();
        if (!$brand) {
            return $this;
        }

        $this->category = $category;

        $data = $this->getOptionData();

        $this->setTitle($data['title'])
            ->setDescription($data['description'])
            ->setImg($data['img_url'])
            ->setCmsBlock($data['cms_block'])
            ->setMetaTitle($data['meta_title'])
            ->setMetaDescription($data['meta_description'])
            ->setMetaKeywords($data['meta_keywords']);
        $category->setData(CategoryDataSetterInterface::APPLIED_BRAND_VALUE, $brand->getValue());
        return $this;
    }

    /**
     * @return array
     */
    private function getOptionData()
    {
        $result = [
            'title' => [],
            'description' => [],
            'cms_block' => null,
            'img_url' => null,
            'meta_title' => [],
            'meta_description' => [],
            'meta_keywords' => [],

        ];

        $setting = $this->brandContentHelper->getCurrentBranding();

        if ($setting->getTitle()) {
            $result['title'][] = $setting->getTitle();
        }
        if ($setting->getDescription()) {
            $result['description'][] = $setting->getDescription();
        }
        if ($setting->getTopCmsBlockId() && $result['cms_block'] === null) {
            $result['cms_block'] = $setting->getTopCmsBlockId();
        }
        if ($setting->getImageUrl() && $result['img_url'] === null) {
            $result['img_url'] = $setting->getImageUrl();
        }

        if ($setting->getMetaTitle()) {
            $result['meta_title'][] = $setting->getMetaTitle();
        }
        if ($setting->getMetaDescription()) {
            $result['meta_description'][] = $setting->getMetaDescription();
        }
        if ($setting->getMetaKeywords()) {
            $result['meta_keywords'][] = $setting->getMetaKeywords();
        }

        return $result;
    }

    /**
     * Set category title.
     * @param array $title
     * @return $this
     */
    private function setTitle($title)
    {
        $title = $this->insertContent(
            '',
            $title,
            self::HEADING_TITLE_SEPARATOR
        );
        $this->category->setName($title);
        return $this;
    }

    /**
     * Set category meta title.
     * @param array $metaTitle
     * @return $this
     */
    private function setMetaTitle($metaTitle)
    {
        $metaTitle = $this->insertContent(
            $this->getOriginPageMetaTitle(),
            $metaTitle,
            self::HEADING_TITLE_SEPARATOR
        );
        $this->category->setData('meta_title', $metaTitle);
        return $this;
    }

    /**
     * @return string
     */
    private function getOriginPageMetaTitle()
    {
        return $this->category->getData('meta_title')
            ? $this->category->getData('meta_title')
            : (string) $this->registry
                ->registry(\Amasty\ShopbyBase\Plugin\View\Page\Title::PAGE_META_TITLE_MAIN_PART);
    }

    /**
     * Set category description.
     * @param array $description
     * @return $this
     */
    private function setDescription($description)
    {
        if ($description) {
            $oldDescription = $this->category->getData('description');
            $description = '<span class="amshopby-descr">' . join('<br>', $description) . '</span>';
            $description = $oldDescription ? $description . '<br>' . $oldDescription : $description;
            $this->category->setData('description', $description);
        }
        return $this;
    }

    /**
     * Set category meta description.
     * @param array $metaDescription
     * @return $this
     */
    private function setMetaDescription(array $metaDescription)
    {
        $metaDescription = $this->insertContent(
            $this->getOriginPageMetaDescription(),
            $metaDescription,
            self::HEADING_DESCRIPTION_SEPARATOR
        );
        $this->category->setData('meta_description', $metaDescription);
        return $this;
    }

    /**
     * @return string
     */
    private function getOriginPageMetaDescription()
    {
        return $this->category->getData('meta_description')
            ? $this->category->getData('meta_description')
            : $this->pageConfig->getDescription();
    }

    /**
     * Set category meta keywords.
     * @param array $metaKeywords
     * @return $this
     */
    private function setMetaKeywords($metaKeywords)
    {

        $metaKeyword = $this->insertContent(
            $this->getOriginPageMetaKeywords(),
            $metaKeywords,
            self::HEADING_TITLE_SEPARATOR
        );
        $this->category->setData('meta_keywords', $metaKeyword);
        return $this;
    }

    /**
     * @return string
     */
    private function getOriginPageMetaKeywords()
    {
        return $this->category->getData('meta_keywords')
            ? $this->category->getData('meta_keywords')
            : $this->pageConfig->getKeywords();
    }

    /**
     * Set category image.
     * @param string|null $imgUrl
     * @return $this
     */
    private function setImg($imgUrl)
    {
        if ($imgUrl !== null) {
            $this->category->setData(CategoryManager::CATEGORY_SHOPBY_IMAGE_URL, $imgUrl);
        }
        return $this;
    }

    /**
     * Set category CMS block.
     * @param string|null $blockId
     * @return $this
     */
    private function setCmsBlock($blockId)
    {
        if ($blockId !== null) {
            $this->category->setData('landing_page', $blockId);
            $this->category->setData(CategoryManager::CATEGORY_FORCE_MIXED_MODE, 1);
        }
        return $this;
    }

    /**
     * replace an original data considering a position and a separator.
     * @param string $original
     * @param array $newParts
     * @param string $separator
     * @return string
     */
    private function insertContent($original, $newParts, $separator)
    {
        if ($newParts) {
            if ($original) {
                array_unshift($newParts, $original);
            }
            $result = join($separator, $newParts);
        } else {
            $result = $original;
        }
        $result = $this->trim($result, $separator);

        return $result;
    }

    /**
     * Trim a string considering a certain separator.
     * @param string $str
     * @param string $separator
     * @return string
     */
    private function trim($str, $separator = ',')
    {
        $str = strip_tags($str);
        $str = str_replace('"', '', $str);
        return trim($str, " " . $separator);
    }
}
