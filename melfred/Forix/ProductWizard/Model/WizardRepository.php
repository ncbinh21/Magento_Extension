<?php


namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Model\ResourceModel\Wizard\CollectionFactory as WizardCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Reflection\DataObjectProcessor;
use Forix\ProductWizard\Api\WizardRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Forix\ProductWizard\Api\Data\WizardInterfaceFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Forix\ProductWizard\Api\Data\WizardSearchResultsInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Store\Model\StoreManagerInterface;
use Forix\ProductWizard\Model\ResourceModel\Wizard as ResourceWizard;

class WizardRepository implements WizardRepositoryInterface
{

    protected $dataObjectProcessor;

    protected $resource;

    protected $dataObjectHelper;

    private $storeManager;

    protected $dataWizardFactory;

    protected $wizardFactory;

    protected $searchResultsFactory;

    protected $wizardCollectionFactory;


    /**
     * @param ResourceWizard $resource
     * @param WizardFactory $wizardFactory
     * @param WizardInterfaceFactory $dataWizardFactory
     * @param WizardCollectionFactory $wizardCollectionFactory
     * @param WizardSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceWizard $resource,
        WizardFactory $wizardFactory,
        WizardInterfaceFactory $dataWizardFactory,
        WizardCollectionFactory $wizardCollectionFactory,
        WizardSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    )
    {
        $this->resource = $resource;
        $this->wizardFactory = $wizardFactory;
        $this->wizardCollectionFactory = $wizardCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataWizardFactory = $dataWizardFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Forix\ProductWizard\Api\Data\WizardInterface $wizard
    )
    {
        if (empty($wizard->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $wizard->setStoreId($storeId);
        }
        try {
            $this->resource->save($wizard);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the wizard: %1',
                $exception->getMessage()
            ));
        }
        return $wizard;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($wizardId)
    {
        $wizard = $this->wizardFactory->create();
        $this->resource->load($wizard, $wizardId);
        if (!$wizard->getId()) {
            throw new NoSuchEntityException(__('Wizard with id "%1" does not exist.', $wizardId));
        }
        return $wizard;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    )
    {
        $collection = $this->wizardCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
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
        \Forix\ProductWizard\Api\Data\WizardInterface $wizard
    )
    {
        try {
            $this->resource->delete($wizard);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Wizard: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($wizardId)
    {
        return $this->delete($this->getById($wizardId));
    }
}
