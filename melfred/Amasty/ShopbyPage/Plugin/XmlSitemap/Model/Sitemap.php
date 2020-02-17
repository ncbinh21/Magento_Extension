<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyPage
 */


namespace Amasty\ShopbyPage\Plugin\XmlSitemap\Model;

use Amasty\XmlSitemap\Model\Sitemap as NativeSitemap;

class Sitemap
{
    /**
     * @var \Amasty\ShopbyPage\Model\ResourceModel\Page\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Amasty\ShopbyPage\Model\ResourceModel\Page\CollectionFactory $collectionFactory
    ) {

        $this->collectionFactory = $collectionFactory;
    }

    public function aroundGetShopByPageCollection(NativeSitemap $subgect, \Closure $proceed, $storeId)
    {
        /** @var \Amasty\ShopbyPage\Model\ResourceModel\Page\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('url', ['neq' => ''])
            ->addStoreFilter($storeId);

        return $collection;
    }
}
