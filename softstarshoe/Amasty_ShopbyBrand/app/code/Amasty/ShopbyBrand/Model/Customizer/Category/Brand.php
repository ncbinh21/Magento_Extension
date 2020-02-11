<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Model\Customizer\Category;

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
     * @var \Amasty\ShopbyBrand\Helper\Data
     */
    private $brandhelper;

    /**
     * @var  Category
     */
    private $category;

    /**
     * @var \Amasty\ShopbyBase\Api\Data\OptionSettingInterface $optionSettings = null
     */
    private $optionSetting = null;

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
     * Brand constructor.
     * @param \Amasty\ShopbyBrand\Helper\Data $brandHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Amasty\ShopbyBase\Helper\OptionSetting $optionSettingHelper
     * @param \Magento\Store\Model\StoreManager $storeManager
     */
    public function __construct(
        \Amasty\ShopbyBrand\Helper\Data $brandHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\App\RequestInterface $request,
        \Amasty\ShopbyBase\Helper\OptionSetting $optionSettingHelper,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Page\Config $pageConfig
    ) {
        $this->brandhelper = $brandHelper;
        $this->layer = $layerResolver->get();
        $this->request = $request;
        $this->optionSettingHelper = $optionSettingHelper;
        $this->storeManager = $storeManager;
        $this->pageConfig = $pageConfig;
    }

    /**
     * @param Category $category
     * @return $this
     */
    public function prepareData(Category $category)
    {
        if (!$this->_getAppliedOptionSetting()) {
            return $this;
        }

        $this->category = $category;

        $data = $this->_getOptionData();

        $this->_setTitle($data['title'])
            ->_setDescription($data['description'])
            ->_setImg($data['img_url'])
            ->_setCmsBlock($data['cms_block'])
            ->_setMetaTitle($data['meta_title'])
            ->_setMetaDescription($data['meta_description'])
            ->_setMetaKeywords($data['meta_keywords']);
        return $this;
    }

    /**
     * @return \Amasty\ShopbyBase\Api\Data\OptionSettingInterface|null
     */
    protected function _getAppliedOptionSetting()
    {
        if ($this->optionSetting === null) {
            $brandAttributeCode = $this->brandhelper->getBrandAttributeCode();
            if ($currentBrandId = $this->request->getParam($brandAttributeCode)) {
                $filterCode = \Amasty\ShopbyBase\Helper\FilterSetting::ATTR_PREFIX . $brandAttributeCode;
                $this->optionSetting = $this->optionSettingHelper->getSettingByValue(
                    $currentBrandId,
                    $filterCode,
                    $this->storeManager->getStore()->getId()
                );
            }
        }
        return $this->optionSetting;
    }

    /**
     * @return array
     */
    protected function _getOptionData()
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

        $setting = $this->_getAppliedOptionSetting();

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
    protected function _setTitle($title)
    {
        $title = $this->_insertContent(
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
    protected function _setMetaTitle($metaTitle)
    {
        $metaTitle = $this->_insertContent(
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
    protected function getOriginPageMetaTitle()
    {
        return $this->category->getData('meta_title')
            ? $this->category->getData('meta_title')
            : (string) $this->pageConfig->getTitle()->getShort();
    }

    /**
     * Set category description.
     * @param array $description
     * @return $this
     */
    protected function _setDescription($description)
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
     * @param array $metaDescriptions
     * @return $this
     */
    protected function _setMetaDescription(array $metaDescription)
    {
        $metaDescription = $this->_insertContent(
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
    protected function getOriginPageMetaDescription()
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
    protected function _setMetaKeywords($metaKeywords)
    {

        $metaKeyword = $this->_insertContent(
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
    protected function getOriginPageMetaKeywords()
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
    protected function _setImg($imgUrl)
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
    protected function _setCmsBlock($blockId)
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
    protected function _insertContent($original, $newParts, $separator)
    {
        if ($newParts) {
            if ($original) {
                array_unshift($newParts, $original);
            }
            $result = join($separator, $newParts);
        } else {
            $result = $original;
        }
        $result = $this->_trim($result, $separator);

        return $result;
    }

    /**
     * Trim a string considering a certain separator.
     * @param string $str
     * @param string $separator
     * @return string
     */
    protected function _trim($str, $separator = ',')
    {
        $str = strip_tags($str);
        $str = str_replace('"', '', $str);
        return trim($str, " " . $separator);
    }
}
