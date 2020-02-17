<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\AdvancedAttribute\Model;

use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\Api\AbstractServiceCollection;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;


/**
 * Tax rule collection for a grid backed by Services
 */
class AttributesCollection extends AbstractServiceCollection
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Magento\Eav\Model\AttributeRepository
     */
    protected $_attributeRepository;

    protected $_collectionFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param TaxRuleRepositoryInterface $ruleService
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory

    )
    {
        parent::__construct($entityFactory, $filterBuilder, $searchCriteriaBuilder, $sortOrderBuilder);
        //$this->_objectManager = $objectManager;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        $collection = $this->_collectionFactory->create()->addVisibleFilter();
        $collection->addFieldToFilter('entity_type_id', '4');
        //$collection->addFieldToFilter('is_filterable', array('neq' => "0"));
        $collection->addFieldToFilter('is_user_defined',array('eq' => 1 ));
        //$collection->addFieldToFilter('frontend_input', array('in' => array('select') ));
        $collection->addFieldToFilter('frontend_input', array('in' => array('select', 'multiselect')));
        if (!$this->isLoaded()) {
            $searchCriteria = $this->getSearchCriteria();
            $fields = [];
            $conditions = [];
            foreach ($searchCriteria->getFilterGroups() as $group) {
                foreach ($group->getFilters() as $filter) {
                    $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                    $fields[] = $filter->getField();
                    $conditions[] = [$condition => $filter->getValue()];
                }
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
            foreach ($collection as $item) {
                $this->_addItem($this->createListAttributeCollectionItem($item));
            }
            $this->_setIsLoaded();
        }
        return $this;
    }

    /**
     * Creates a collection item that represents a tax rule for the tax rules grid.
     *
     * @param TaxRuleInterface $taxRule Input data for creating the item.
     * @return \Magento\Framework\DataObject Collection item that represents a tax rule
     */
    protected function createListAttributeCollectionItem($item)
    {
        $collectionItem = new \Magento\Framework\DataObject();
        $collectionItem->setAttrid((string)$item->getId());
        $collectionItem->setAttrcode((string)$item->getAttributeCode());

        $collectionItem->setAttributeCode((string)$item->getAttributeCode());
        /* should cast to string so that some optional fields won't be null on the collection grid pages */
        $collectionItem->setAttributeName((string)$item->getFrontendLabel());

        return $collectionItem;
    }

}
