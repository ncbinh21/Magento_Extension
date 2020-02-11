<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/16/2016
 * Time: 10:23 AM
 */

namespace Forix\SearchNoResult\Observer;

use Magento\Framework\Event\ObserverInterface;

class SearchLayoutLoadBeforeObserver implements ObserverInterface
{
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    private $searchEngine;

    private $requestBuilder;

    private $storeManager;

    private $helper;

    /**
     * SearchLayoutLoadBeforeObserver constructor.
     * @param \Magento\Search\Model\QueryFactory $queryFactory
     * @param \Magento\Search\Model\SearchEngine $searchEngine
     * @param \Magento\Framework\Search\Request\Builder $requestBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Search\Model\QueryFactory $queryFactory,
        \Magento\Search\Model\SearchEngine $searchEngine,
        \Magento\Framework\Search\Request\Builder $requestBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Forix\SearchNoResult\Helper\Data $searchNoResultHelper
    ) {
        $this->queryFactory = $queryFactory;
        $this->searchEngine = $searchEngine;
        $this->requestBuilder = $requestBuilder;
        $this->storeManager = $storeManager;
        $this->helper = $searchNoResultHelper;
    }

    /**
     * @return $this|\Magento\Search\Model\Query|\Magento\Search\Model\QueryInterface
     */
    protected function _getQuery()
    {
        return $this->queryFactory->get();
    }

    /**
     * Set empty layout for search no result page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        if($this->helper->isEnabled() && $observer->getFullActionName() == 'catalogsearch_result_index') {
            $queryRequest = $this->requestBuilder
                ->bindDimension('scope', $this->storeManager->getStore()->getId())
                ->bind('search_term', $this->_getQuery()->getQueryText())
                ->setRequestName('quick_search_container')
                ->create();
            $queryResponse = $this->searchEngine->search($queryRequest);

            if(!$queryResponse->count()) {
                $observer->getLayout()
                    ->getUpdate()
                    ->addHandle('catalogsearch_result_index_empty');
                return $this;
            }
        }
    }
}