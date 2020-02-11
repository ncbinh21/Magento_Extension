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



namespace  Mirasvit\SeoAutolink\Ui\SeoAutolink\Listing;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @param  ResourceConnection $resource
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceConnection $resource,
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        $this->connection = $resource;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult)
    {
        $arrItems = [];
        $arrItems['totalRecords'] = $searchResult->getTotalCount();
        $arrItems['items'] = [];

        $storeIds = [];
        if ($data = $searchResult->getData()) { //prepare store_id for multistore
            foreach ($data as $value) {
                $storeIds[$value['link_id']] = $value['store_id'];
            }
        }

        foreach ($searchResult->getItems() as $item) {
            if (isset($storeIds[$item->getId()])) {  //prepare store_id for multistore
                $item->setData('store_id', $storeIds[$item->getId()]);
            }
            $arrItems['items'][] = $item->getData();

        }

        return $arrItems;
    }

    /**
     * Returns Search result
     *
     * @return SearchResultInterface
     */
    public function getSearchResult()
    {
        $groups     = [];
        $fieldStoreValue = '';

        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($this->getSearchCriteria()->getFilterGroups() as $group) {
            if (empty($group->getFilters())) {
                continue;
            }
            $filters = [];
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == 'store_id') {
                    $fieldStoreValue = $filter->getValue();
                    continue;
                }
                $filters[] = $filter;
            }
            $group->setFilters($filters);
            $groups[] = $group;
        }
        $this->getSearchCriteria()->setFilterGroups($groups);

        $collection = $this->getPreparedCollection($fieldStoreValue);

        return $collection;
    }

    /**
     * @param string $fieldStoreValue
     * @return Mirasvit\SeoAutolink\Model\ResourceModel\Link\Grid\Collection
     */
    protected function getPreparedCollection($fieldStoreValue)
    {
        $collection = $this->reporting->search($this->getSearchCriteria());

        if ($fieldStoreValue) {
            $linkIds = $this->getLinkIds($fieldStoreValue);
            $collection->addStoreColumn()->getSelect()
                ->where(
                    new \Zend_Db_Expr('link_id IN (' . implode(',', $linkIds) . ')')
                );

        }

        return $collection;
    }

    /**
     * @param string $fieldStoreValue
     * @return array
     */
    protected function getLinkIds($fieldStoreValue)
    {
        $query = 'SELECT link_id FROM '
            . $this->connection->getTableName('mst_seoautolink_link_store')
            . ' WHERE store_id IN (' . addslashes(implode(',', $fieldStoreValue)) . ')';

        $storeData = $this->connection->getConnection('read')->fetchAll($query);
        $linkIds = [];
        foreach ($storeData as $store) {
            $linkIds[] = $store['link_id'];
        }

        if (!$linkIds) {
            $linkIds[] = 0;
        }

        return $linkIds;
    }

}
