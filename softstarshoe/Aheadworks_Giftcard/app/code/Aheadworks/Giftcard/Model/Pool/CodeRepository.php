<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Pool;

use Aheadworks\Giftcard\Api\PoolCodeRepositoryInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\EntityManager\EntityManager;
use Aheadworks\Giftcard\Model\Pool\Code as CodeModel;
use Aheadworks\Giftcard\Api\Data\Pool\CodeInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Pool\CodeSearchResultsInterface;
use Aheadworks\Giftcard\Api\Data\Pool\CodeSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class CodeRepository
 *
 * @package Aheadworks\Giftcard\Model\Pool
 */
class CodeRepository implements PoolCodeRepositoryInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var CodeFactory
     */
    private $codeFactory;

    /**
     * @var CodeInterfaceFactory
     */
    private $codeDataFactory;

    /**
     * @var CodeSearchResultsInterfaceFactory
     */
    private $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var JoinProcessorInterface
     */
    private $extensionAttributesJoinProcessor;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * @param EntityManager $entityManager
     * @param CodeFactory $codeFactory
     * @param CodeInterfaceFactory $codeDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param CodeSearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        EntityManager $entityManager,
        CodeFactory $codeFactory,
        CodeInterfaceFactory $codeDataFactory,
        DataObjectHelper $dataObjectHelper,
        CodeSearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->entityManager = $entityManager;
        $this->codeFactory = $codeFactory;
        $this->codeDataFactory = $codeDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function get($codeId)
    {
        if (!isset($this->registry[$codeId])) {
            /** @var CodeInterface $code */
            $code = $this->codeDataFactory->create();
            $this->entityManager->load($code, $codeId);
            if (!$code->getId()) {
                throw NoSuchEntityException::singleField('codeId', $codeId);
            }
            $this->registry[$codeId] = $code;
        }
        return $this->registry[$codeId];
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var CodeSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create()
            ->setSearchCriteria($searchCriteria);
        /** @var \Aheadworks\Giftcard\Model\ResourceModel\Pool\Code\Collection $collection */
        $collection = $this->codeFactory->create()->getCollection();
        $this->extensionAttributesJoinProcessor->process($collection, CodeInterface::class);
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        if ($sortOrders = $searchCriteria->getSortOrders()) {
            /** @var \Magento\Framework\Api\SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }

        $collection
            ->setCurPage($searchCriteria->getCurrentPage())
            ->setPageSize($searchCriteria->getPageSize());

        $codes = [];
        /** @var CodeModel $codeModel */
        foreach ($collection as $codeModel) {
            $codes[] = $this->getCodeDataObject($codeModel);
        }
        $searchResults->setItems($codes);
        return $searchResults;
    }

    /**
     * {@inheritDoc}
     */
    public function delete(CodeInterface $code)
    {
        return $this->deleteById($code->getId());
    }

    /**
     * {@inheritDoc}
     */
    public function deleteById($codeId)
    {
        $code = $this->get($codeId);
        $this->entityManager->delete($code);
        if (isset($this->registry[$codeId])) {
            unset($this->registry[$codeId]);
        }
        return true;
    }

    /**
     * Retrieves code data object using code model
     *
     * @param CodeModel $code
     * @return CodeInterface
     */
    private function getCodeDataObject(CodeModel $code)
    {
        /** @var CodeInterface $codeDataObject */
        $codeDataObject = $this->codeDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $codeDataObject,
            $code->getData(),
            CodeInterface::class
        );
        return $codeDataObject;
    }
}
