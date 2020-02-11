<?php
/**
 * Hidro Forix Webdesign. 
 * Copyright (C) 2017  Hidro Le
 * 
 * This file included in Forix/QuoteLetter is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Forix\QuoteLetter\Model;

use Forix\QuoteLetter\Api\Data\QuoteLetterSearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Forix\QuoteLetter\Api\QuoteLetterRepositoryInterface;
use Magento\Framework\Api\SortOrder;
use Forix\QuoteLetter\Model\ResourceModel\QuoteLetter\CollectionFactory as QuoteLetterCollectionFactory;
use Forix\QuoteLetter\Model\ResourceModel\QuoteLetter as ResourceQuoteLetter;
use Magento\Store\Model\StoreManagerInterface;
use Forix\QuoteLetter\Api\Data\QuoteLetterInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;

class QuoteLetterRepository implements quoteLetterRepositoryInterface
{

    protected $quoteLetterFactory;

    protected $quoteLetterCollectionFactory;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    protected $dataQuoteLetterFactory;

    protected $searchResultsFactory;

    protected $resource;

    private $storeManager;


    /**
     * @param ResourceQuoteLetter $resource
     * @param QuoteLetterFactory $quoteLetterFactory
     * @param QuoteLetterInterfaceFactory $dataQuoteLetterFactory
     * @param QuoteLetterCollectionFactory $quoteLetterCollectionFactory
     * @param QuoteLetterSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceQuoteLetter $resource,
        QuoteLetterFactory $quoteLetterFactory,
        QuoteLetterInterfaceFactory $dataQuoteLetterFactory,
        QuoteLetterCollectionFactory $quoteLetterCollectionFactory,
        QuoteLetterSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->quoteLetterFactory = $quoteLetterFactory;
        $this->quoteLetterCollectionFactory = $quoteLetterCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataQuoteLetterFactory = $dataQuoteLetterFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\QuoteLetter\Api\Data\QuoteLetterInterface $quoteLetter
    ) {
        /* if (empty($quoteLetter->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $quoteLetter->setStoreId($storeId);
        } */
        try {
            $quoteLetter->getResource()->save($quoteLetter);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the quoteLetter: %1',
                $exception->getMessage()
            ));
        }
        return $quoteLetter;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($quoteLetterId)
    {
        $quoteLetter = $this->quoteLetterFactory->create();
        $quoteLetter->getResource()->load($quoteLetter, $quoteLetterId);
        if (!$quoteLetter->getId()) {
            throw new NoSuchEntityException(__('QuoteLetter with id "%1" does not exist.', $quoteLetterId));
        }
        return $quoteLetter;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->quoteLetterCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Forix\QuoteLetter\Api\Data\QuoteLetterInterface $quoteLetter
    ) {
        try {
            $quoteLetter->getResource()->delete($quoteLetter);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the QuoteLetter: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($quoteLetterId)
    {
        return $this->delete($this->getById($quoteLetterId));
    }
}
