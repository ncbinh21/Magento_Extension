<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Amasty\Fpc\Model\Config\Source\PageType;
use Magento\Cms\Helper\Page as PageHelper;
use Magento\Cms\Model\ResourceModel\Page\Collection as PageCollection;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;

class Refresher
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    private $configCollectionFactory;
    /**
     * @var QueuePageRepository
     */
    private $pageRepository;
    /**
     * @var Source\PageType\Factory
     */
    private $pageTypeFactory;
    /**
     * @var ResourceModel\Queue\Page
     */
    private $pageResource;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configCollectionFactory,
        \Amasty\Fpc\Model\QueuePageRepository $pageRepository,
        \Amasty\Fpc\Model\Source\PageType\Factory $pageTypeFactory,
        \Amasty\Fpc\Model\ResourceModel\Queue\Page $pageResource
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->configCollectionFactory = $configCollectionFactory;
        $this->pageRepository = $pageRepository;
        $this->pageTypeFactory = $pageTypeFactory;
        $this->pageResource = $pageResource;
    }

    public function queueIndexPage()
    {
        $type = $this->pageTypeFactory->create(PageType::TYPE_INDEX);
        $this->queuePages($type->getAllPages());
    }

    public function queueCmsPage($identifier)
    {
        $filter = function(PageCollection $collection) use ($identifier) {
            $collection->addFieldToFilter('identifier', $identifier);
        };

        $type = $this->pageTypeFactory->create(PageType::TYPE_CMS, [
            'filterCollection' => $filter
        ]);

        $this->queuePages($type->getAllPages());
    }

    public function queueProductPage($id)
    {
        $filter = function(UrlRewriteCollection $collection) use ($id) {
            $collection->addFieldToFilter('entity_id', $id);
        };

        $type = $this->pageTypeFactory->create(PageType::TYPE_PRODUCT, [
            'filterCollection' => $filter
        ]);

        $this->queuePages($type->getAllPages());
    }

    public function queueCategoryPage($id)
    {
        $filter = function(UrlRewriteCollection $collection) use ($id) {
            $collection->addFieldToFilter('entity_id', $id);
        };

        $type = $this->pageTypeFactory->create(PageType::TYPE_CATEGORY, [
            'filterCollection' => $filter
        ]);

        $this->queuePages($type->getAllPages());
    }

    public function isIndexPage($identifier)
    {
        if ($identifier == $this->scopeConfig->getValue(PageHelper::XML_PATH_HOME_PAGE)) {
            // Default value
            return true;
        }

        /** @var \Magento\Config\Model\ResourceModel\Config\Data\Collection $configCollection */
        $configCollection = $this->configCollectionFactory->create();

        $configCollection
            ->addFieldToFilter('path', PageHelper::XML_PATH_HOME_PAGE)
            ->addValueFilter($identifier);

        return (bool)$configCollection->getSize();
    }

    protected function queuePages(array $pages)
    {
        $rate = $this->getMaxRate() + 1;

        foreach ($pages as $page) {
            $page['rate'] = $rate;
            $this->pageRepository->addPage($page);
        }
    }

    protected function getMaxRate()
    {
        return $this->pageResource->getMaxRate();
    }
}
