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


namespace Mirasvit\Seo\Ui\Template\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\ResourceConnection;

class SeoTemplateGridDataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ResourceConnection $resource,
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Magento\Framework\Registry $registry,
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
        $this->registry = $registry;
        $this->request = $request;
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
                $storeIds[$value['template_id']] = $value['store_id'];
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
     * @return \Mirasvit\Seo\Model\ResourceModel\Template\Collection
     */
    public function getSearchResult()
    {
        $groups     = [];
        $fieldValue = '';
        $fieldStoreValue = '';

        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($this->getSearchCriteria()->getFilterGroups() as $group) {
            if (empty($group->getFilters())) {
                continue;
            }
            $filters = [];
            /** @var \Magento\Framework\Api\Filter $filter */
            foreach ($group->getFilters() as $filter) {
                if ($filter->getField() == 'template_settings') {
                    $fieldValue = $filter->getValue();
                    continue;
                }
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

        $collection = $this->getPreparedCollection($fieldValue, $fieldStoreValue);

        return $collection;
    }

    /**
     * @param string $fieldValue
     * @param string $fieldStoreValue
     * @return Mirasvit\Seo\Model\ResourceModel\Redirect\Grid\Collection
     */
    protected function getPreparedCollection($fieldValue, $fieldStoreValue)
    {
        $collection = $this->reporting->search($this->getSearchCriteria());

        if ($fieldValue) {
            $collection->getSelect()->where(
                'meta_title like ?
                        OR meta_keywords like ?
                        OR meta_description like ?
                        OR title like ?
                        OR description like ?
                        OR short_description like ?
                        OR full_description like ?',
                '%' . $fieldValue . '%'
            );
        }

        if ($fieldStoreValue) {
            $templateIds = $this->getTemplateIds($fieldStoreValue);
            $collection->addStoreColumn()->getSelect()
                ->where(
                    new \Zend_Db_Expr('template_id IN (' . implode(',', $templateIds) . ')')
                );

        }

        return $collection;
    }

    /**
     * @param string $fieldStoreValue
     * @return array
     */
    protected function getTemplateIds($fieldStoreValue)
    {
        $query = 'SELECT template_id FROM '
            . $this->connection->getTableName('mst_seo_template_store')
            . ' WHERE store_id IN (' . addslashes(implode(',', $fieldStoreValue)) . ')';

        $storeData = $this->connection->getConnection('read')->fetchAll($query);
        $linkIds = [];
        foreach ($storeData as $store) {
            $linkIds[] = $store['template_id'];
        }

        if (!$linkIds) {
            $linkIds[] = 0;
        }

        return $linkIds;
    }
}
