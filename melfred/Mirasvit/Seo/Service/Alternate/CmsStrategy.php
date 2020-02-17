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



namespace Mirasvit\Seo\Service\Alternate;

use Mirasvit\Seo\Api\Config\AlternateConfigInterface as AlternateConfig;

class CmsStrategy implements \Mirasvit\Seo\Api\Service\Alternate\StrategyInterface
{
    /**
     * @var \Mirasvit\Seo\Api\Service\Alternate\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $page;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Mirasvit\Seo\Helper\Version
     */
    protected $version;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * @var \Mirasvit\Seo\Api\Config\AlternateConfigInterface
     */
    protected $alternateConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url
     * @param \Magento\Cms\Model\Page $page
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Mirasvit\Seo\Helper\Version $version
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Mirasvit\Seo\Api\Service\Alternate\UrlInterface $url,
        \Magento\Cms\Model\Page $page,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \Magento\Framework\App\Request\Http $request,
        \Mirasvit\Seo\Helper\Version $version,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->url = $url;
        $this->page = $page;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->request = $request;
        $this->version = $version;
        $this->resource = $resource;
        $this->alternateConfig = $alternateConfig;
        $this->storeManager = $storeManager;
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
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAlternateUrl($storeUrls)
    {
        $cmsPageId = $this->page->getPageId();
        $alternateGroupInstalled = false;
        $cmsStoresIds = $this->page->getStoreId();

        //check if alternate_group exist in cms_page table
        if (($pageObject = $this->pageCollectionFactory->create()->getItemById($cmsPageId))
            && is_object($pageObject) && ($pageObjectData = $pageObject->getData())
            && array_key_exists('alternate_group', $pageObjectData)) {
            $alternateGroupInstalled = true;
        }

        if (!$alternateGroupInstalled) {
            return $storeUrls;
        }

        $cmsCollection = $this->pageCollectionFactory->create()
            ->addFieldToSelect('alternate_group')
            ->addFieldToFilter('page_id', ['eq' => $cmsPageId])
            ->getFirstItem();

        $alternateGroup = $cmsCollection->getAlternateGroup();

        if ($cmsStoresIds[0] == 0 //use if alternate groups configured and use alternate configuration
            && $alternateGroup
            && ($storeId = $this->storeManager->getStore()->getStoreId())
            && $this->alternateConfig->getAlternateHreflang($storeId) == AlternateConfig::ALTERNATE_CONFIGURABLE
            && ($stores = $this->alternateConfig->getAlternateManualConfig($storeId, false))) {
                $cmsPages = $this->getCmsPages($alternateGroup);

            if (count($cmsPages) > 0) {
                $storeUrls = []; // use only links with alternate_group
                $pageStoreData= [];
                foreach ($cmsPages as $page) {
                    $pageStoreData[$page['store_id']] = $page;
                }

                foreach ($stores as $store) {
                    if (isset($this->url->getStores()[$store])) {
                        $page = isset($pageStoreData[$store]) ? $pageStoreData[$store] : $pageStoreData[0];
                        $pageIdentifier = $page['identifier'];
                        $fullAction = $this->request->getFullActionName();

                        $baseStoreUrl = $this->url->getStores()[$store]->getBaseUrl();

                        $storeUrls[$store] = ($fullAction == 'cms_index_index') ? $baseStoreUrl
                            : $baseStoreUrl . $pageIdentifier;
                    }
                }
            }
        } elseif ($alternateGroup && $cmsStoresIds[0] != 0) {
            $cmsPages = $this->getCmsPages($alternateGroup);

            if (count($cmsPages) > 0) {
                $storeUrls = []; // use only links with alternate_group
                foreach ($cmsPages as $page) {
                    if (isset($this->url->getStores()[$page['store_id']])) {
                        $pageIdentifier = $page['identifier'];
                        $fullAction = $this->request->getFullActionName();
                        $baseStoreUrl = $this->url->getStores()[$page['store_id']]->getBaseUrl();
                        $storeUrls[$page['store_id']] = ($fullAction == 'cms_index_index') ? $baseStoreUrl
                            : $baseStoreUrl . $pageIdentifier;
                    }
                }
            }
        }

        return $storeUrls;
    }

    /**
     * @param string $alternateGroup
     * @return array
     */
    protected function getCmsPages($alternateGroup)
    {
        $cmsCollection = $this->pageCollectionFactory->create()
            ->addFieldToSelect(['alternate_group', 'identifier'])
            ->addFieldToFilter('alternate_group', ['eq' => $alternateGroup])
            ->addFieldToFilter('is_active', true);
        $table = $this->resource->getTableName('cms_page_store');
        $storeTablePageId = ($this->version->isEe()) ? 'row_id' : 'page_id';
        $cmsCollection->getSelect()
            ->join(
                [
                    'storeTable' => $table],
                'main_table.page_id = storeTable.' . $storeTablePageId,
                ['store_id' => 'storeTable.store_id']
            );
        $cmsPages = $cmsCollection->getData();

        return $cmsPages;
    }
}