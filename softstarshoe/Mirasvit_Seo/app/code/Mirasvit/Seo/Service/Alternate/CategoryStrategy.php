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

class CategoryStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    /**
     * @var \Mirasvit\Seo\Api\Service\Alternate\UrlInterface
     */
    protected $url;

    /**
     * @var \Mirasvit\Seo\Api\Config\AlternateConfigInterface
     */
    protected $alternateConfig;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
     * @param \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url,
        \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->url = $url;
        $this->alternateConfig = $alternateConfig;
        $this->context = $context;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreUrls()
    {
        $storeUrls = $this->url->getStoresCurrentUrl();
        $storeUrls = $this->getAlternateUrl($storeUrls);

        return $storeUrls;
    }

    /**
     * {@inheritdoc}
     */
    public function getAlternateUrl($storeUrls)
    {
        $currentBaseUrl = $this->context->getUrlBuilder()->getBaseUrl();
        foreach ($this->url->getStores() as $storeId => $store) {
            $currentUrl = $this->context->getUrlBuilder()->getCurrentUrl();
            $category = $this->categoryCollectionFactory->create()
                ->setStoreId($store->getId())
                ->addFieldToFilter('is_active', ['eq' => '1'])
                ->addFieldToFilter('entity_id', ['eq' => $this->registry->registry('current_category')->getId()])
                ->getFirstItem();
            if ($category->hasData() && ($currentCategory = $this->categoryFactory
                    ->create()
                    ->setStoreId($store->getId())
                    ->load($category->getEntityId()))
            ) {
                $storeBaseUrl = $store->getBaseUrl();
                $currentCategoryUrl = $currentCategory->getUrl();
                //ned for situation like https://example.com/eu/ and https://example.com/
                $currentCategoryUrl = str_replace($currentBaseUrl, $storeBaseUrl, $currentCategoryUrl);
                // correct suffix for every store can't be added, because magento works incorrect,
                // maybe after magento fix (if need)
                if (strpos($currentCategoryUrl, $storeBaseUrl) === false) {
                    //create correct category way for every store, need if category use different path
                    $slashStoreBaseUrlCount = substr_count($storeBaseUrl, '/');
                    $currentCategoryUrlExploded = explode('/', $currentCategoryUrl);
                    $currentCategoryUrl = $storeBaseUrl.implode(
                            '/',
                            array_slice($currentCategoryUrlExploded, $slashStoreBaseUrlCount)
                        );
                }

                $urlAddition = $this->url->getUrlAddition($store);

                $preparedUrlAdditionCurrent = $this->getUrlAdditionalParsed(strstr($currentUrl, '?'));
                $preparedUrlAdditionStore = $this->getUrlAdditionalParsed($urlAddition);
                $urlAdditionCategory = $this->getPreparedUrlAdditional(
                    $preparedUrlAdditionCurrent,
                    $preparedUrlAdditionStore
                );
                // if store use different attributes name will be added after use seo filter (if need)
                if ($this->alternateConfig->isHreflangCutCategoryAdditionalData()) {
                    $storeUrls[$storeId] = $currentCategoryUrl;
                } else {
                    $storeUrls[$storeId] = $currentCategoryUrl . $urlAdditionCategory;
                }
            }
        }

        return $storeUrls;
    }

    /**
     * Parse additional  url.
     *
     * @param string $urlAddition
     *
     * @return array
     */
    protected function getUrlAdditionalParsed($urlAddition)
    {
        if (!$urlAddition) {
            return [];
        }
        $preparedUrlAddition = [];
        $urlAdditionParsed = (substr($urlAddition, 0, 1) == '?') ? substr($urlAddition, 1) : $urlAddition;
        $urlAdditionParsed = explode('&', $urlAdditionParsed);
        foreach ($urlAdditionParsed as $urlAdditionValue) {
            if (strpos($urlAdditionValue, '=') !== false) {
                $urlAdditionValueArray = explode('=', $urlAdditionValue);
                $preparedUrlAddition[$urlAdditionValueArray[0]] = $urlAdditionValueArray[1];
            } else {
                $preparedUrlAddition[$urlAdditionValue] = '';
            }
        }

        return $preparedUrlAddition;
    }

    /**
     * Prepare additional  url.
     *
     * @param array $preparedUrlAdditionCurrent
     * @param array $preparedUrlAdditionStore
     *
     * @return string
     */
    protected function getPreparedUrlAdditional($preparedUrlAdditionCurrent, $preparedUrlAdditionStore)
    {
        $correctUrlAddition = [];
        $mergedUrlAddition = array_merge_recursive($preparedUrlAdditionCurrent, $preparedUrlAdditionStore);
        foreach ($mergedUrlAddition as $keyUrlAddition => $valueUrlAddition) {
            if (is_array($valueUrlAddition) && $keyUrlAddition == '___store') {
                $correctUrlAddition[$keyUrlAddition] = $valueUrlAddition[1];
            } elseif (is_array($valueUrlAddition)) {
                $correctUrlAddition[$keyUrlAddition] = $valueUrlAddition[0];
            } elseif (array_key_exists($keyUrlAddition, $preparedUrlAdditionCurrent) || $keyUrlAddition == '___store') {
                $correctUrlAddition[$keyUrlAddition] = $valueUrlAddition;
            }
        }
        $urlAddition = (count($correctUrlAddition) > 0) ? $this->getUrlAdditionalString($correctUrlAddition) : '';

        return $urlAddition;
    }

    /**
     * Convert additional url array to string.
     *
     * @param array $correctUrlAddition
     *
     * @return string
     */
    protected function getUrlAdditionalString($correctUrlAddition)
    {
        $urlAddition = '?';
        $urlAdditionArray = [];
        foreach ($correctUrlAddition as $keyUrlAddition => $valueUrlAddition) {
            $urlAdditionArray[] .= $keyUrlAddition.'='.$valueUrlAddition;
        }
        $urlAddition .= implode('&', $urlAdditionArray);

        return $urlAddition;
    }

}