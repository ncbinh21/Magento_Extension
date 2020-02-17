<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Shopby\Model\Layer;

use Magento\Catalog\Model\Layer\FilterableAttributeListInterface;

class FilterList extends \Magento\Catalog\Model\Layer\FilterList
{

	protected $filterTypes = [
		self::CATEGORY_FILTER  => \Forix\Shopby\Model\Layer\Filter\Category::class,
	];

    public function __construct(
		\Magento\Framework\ObjectManagerInterface $objectManager,
		FilterableAttributeListInterface $filterableAttributes,
		array $filters = []
	) {
		parent::__construct($objectManager, $filterableAttributes, $filters);
	}


    public function getFilters(\Magento\Catalog\Model\Layer $layer)
    {

        if (!count($this->filters)) {
            $this->filters = [
                $this->objectManager->create($this->filterTypes[self::CATEGORY_FILTER], ['layer' => $layer]),
            ];

        }

        return $this->filters;
    }

    /**
     * Create filter
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @param \Magento\Catalog\Model\Layer $layer
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    protected function createAttributeFilter(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute,
        \Magento\Catalog\Model\Layer $layer
    ) {
        $filterClassName = $this->getAttributeFilterClass($attribute);

        $filter = $this->objectManager->create(
            $filterClassName,
            ['data' => ['attribute_model' => $attribute], 'layer' => $layer]
        );
        return $filter;
    }

    /**
     * Get Attribute Filter Class Name
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return string
     */
    protected function getAttributeFilterClass(\Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $filterClassName = $this->filterTypes[self::ATTRIBUTE_FILTER];

        if ($attribute->getAttributeCode() == 'price') {
            $filterClassName = $this->filterTypes[self::PRICE_FILTER];
        } elseif ($attribute->getBackendType() == 'decimal') {
            $filterClassName = $this->filterTypes[self::DECIMAL_FILTER];
        }

        return $filterClassName;
    }


}
