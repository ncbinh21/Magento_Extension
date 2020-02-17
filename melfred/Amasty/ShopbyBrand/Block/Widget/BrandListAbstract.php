<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Block\Widget;

use Amasty\ShopbyBrand\Helper\Data;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\View\Element\Template\Context;
use Amasty\ShopbyBase\Helper\OptionSetting as OptionSettingHelper;
use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Eav\Model\Entity\Attribute\Option;
use Amasty\ShopbyBrand\Helper\Data as DataHelper;
use \Magento\Framework\Exception\StateException;

abstract class BrandListAbstract extends \Magento\Framework\View\Element\Template
{
    const PATH_BRAND_ATTRIBUTE_CODE = 'amshopby_brand/general/attribute_code';

    /**
     * @var  array
     */
    protected $items;

    /**
     * @var  Repository
     */
    protected $repository;

    /**
     * @var OptionSettingHelper
     */
    protected $optionSettingHelper;

    /**
     * @var \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider
     */
    protected $collectionProvider;

    /**
     * @var \Magento\Catalog\Model\Product\Url
     */
    protected $productUrl;

    /**
     * @var array|null
     */
    protected $productCount = null;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     * @var DataHelper
     */
    private $helper;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * BrandListAbstract constructor.
     * @param Context $context
     * @param Repository $repository
     * @param OptionSettingHelper $optionSetting
     * @param \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $collectionProvider
     * @param \Magento\Catalog\Model\Product\Url $productUrl
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param DataHelper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Repository $repository,
        OptionSettingHelper $optionSetting,
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $collectionProvider,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        DataHelper $helper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->repository = $repository;
        $this->optionSettingHelper = $optionSetting;
        $this->collectionProvider = $collectionProvider;
        $this->productUrl = $productUrl;
        $this->categoryRepository = $categoryRepository;
        $this->helper = $helper;
        $this->messageManager = $messageManager;
    }

    /**
     * @return array|null
     */
    public function getItems()
    {
        if ($this->items === null) {
            $this->items = [];
            $attributeCode = $this->_scopeConfig->getValue(
                self::PATH_BRAND_ATTRIBUTE_CODE,
                ScopeInterface::SCOPE_STORE
            );
            if ($attributeCode == '') {
                return $this->items;
            }

            $options = $this->repository->get($attributeCode)->getOptions();
            array_shift($options);

            foreach ($options as $option) {
                $filterCode = \Amasty\ShopbyBase\Helper\FilterSetting::ATTR_PREFIX . $attributeCode;
                $setting = $this->optionSettingHelper->getSettingByValue(
                    $option->getValue(),
                    $filterCode,
                    $this->_storeManager->getStore()->getId()
                );

                $data = $this->getItemData($option, $setting);
                if ($data) {
                    $this->items[] = $data;
                }
            }

        }

        return $this->items;
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Option $option
     * @param \Amasty\ShopbyBase\Api\Data\OptionSettingInterface $setting
     * @return array
     */
    abstract protected function getItemData(Option $option, OptionSettingInterface $setting);

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Option $option
     * @return string
     */
    public function getBrandUrl(Option $option)
    {
        return $this->helper->getBrandUrl($option);
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortByName($a, $b)
    {
        $a['label'] = trim($a['label']);
        $b['label'] = trim($b['label']);

        if ($a == '') {
            return 1;
        }
        if ($b == '') {
            return -1;
        }

        $x = substr($a['label'], 0, 1);
        $y = substr($b['label'], 0, 1);
        if (is_numeric($x) && !is_numeric($y)) {
            return 1;
        }
        if (!is_numeric($x) && is_numeric($y)) {
            return -1;
        }

        if (function_exists('mb_strtoupper')) {
            $res = strcmp(mb_strtoupper($a['label']), mb_strtoupper($b['label']));
        } else {
            $res = strcmp(strtoupper($a['label']), strtoupper($b['label']));
        }
        return $res;
    }

    /**
     * Apply options from config
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $configValues = $this->_scopeConfig->getValue(
            $this->getConfigValuesPath(),
            ScopeInterface::SCOPE_STORE
        );
        foreach ($configValues as $option => $value) {
            if ($this->getData($option) === null) {
                $this->setData($option, $value);
            }
        }

        $this->applySorting();

        return parent::_beforeToHtml();
    }

    /**
     * Apply additional sorting before render html
     *
     * @return $this
     */
    protected function applySorting()
    {
        return $this;
    }

    /**
     * @return string
     */
    abstract protected function getConfigValuesPath();

    /**
     * Get brand product count
     *
     * @param int $optionId
     * @return int
     */
    protected function _getOptionProductCount($optionId)
    {
        if ($this->productCount === null) {
            $rootCategoryId = $this->_storeManager->getStore()->getRootCategoryId();
            $category = $this->categoryRepository->get($rootCategoryId);
            /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection */
            $collection = $this->collectionProvider->getCollection($category);
            $attrCode = $this->_scopeConfig->getValue(
                'amshopby_brand/general/attribute_code',
                ScopeInterface::SCOPE_STORE
            );

            try {
                $this->productCount = $collection
                    ->addAttributeToSelect($attrCode)
                    ->setVisibility([2,4])
                    ->getFacetedData($attrCode);
            } catch (StateException $e) {
                if (!$this->messageManager->hasMessages()) {
                    $this->messageManager->addErrorMessage(
                        __('Make sure that the root category for current store is anchored')
                    )->addErrorMessage(
                        __('Make sure that "%1" attribute can be used in layered navigation', $attrCode)
                    );
                }
                $this->productCount = [];
            }

        }

        return isset($this->productCount[$optionId]) ? $this->productCount[$optionId]['count'] : 0;
    }
}
