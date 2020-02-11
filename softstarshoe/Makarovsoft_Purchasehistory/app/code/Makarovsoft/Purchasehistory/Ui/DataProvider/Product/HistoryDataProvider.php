<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Makarovsoft\Purchasehistory\Ui\DataProvider\Product;

use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\Collection;

use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;

/**
 * Class ReviewDataProvider
 *
 * @method Collection getCollection
 */
class HistoryDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    protected $_orderItemFactory;

    protected $searchCriteriaBuilder;

    /**
     * @var SearchCriteria
     */
    protected $searchCriteria;

    protected $document;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Document $doc,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collectionFactory = $collectionFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->document = $doc;
        $this->collection = $this->collectionFactory->create();
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('product_id', $this->request->getParam('current_product_id', 0));

        $tableAlias = 'sales_order_collection';
        $conditions = array(
            "{$tableAlias}.entity_id = main_table.order_id",
        );

        $this->getCollection()->getSelect()->join(
            array($tableAlias => $this->getCollection()->getTable('sales_order_grid')),
            implode(' AND ', $conditions),
            array("{$tableAlias}.store_id",
                "{$tableAlias}.entity_id",
                "{$tableAlias}.increment_id",
                "{$tableAlias}.status",
                "{$tableAlias}.customer_name",
                "{$tableAlias}.customer_email",
                "{$tableAlias}.grand_total",
                "{$tableAlias}.order_currency_code"
            )
        );

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }

    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName('history_listing_data_source');
        }
        return $this->searchCriteria;
    }


    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        parent::addFilter($filter);
    }
}
