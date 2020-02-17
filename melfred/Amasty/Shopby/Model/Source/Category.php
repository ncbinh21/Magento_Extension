<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Model\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Model\Category as CatalogCategory;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;

/**
 * Class Category
 * @package Amasty\Shopby\Model\Source
 */
class Category implements ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var bool
     */
    private $emptyOption = true;

    /**
     * Category constructor.
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }
        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [];

        if ($this->emptyOption) {
            $options[0] = ' ';
        }

        $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('name');
        $collection->addAttributeToFilter('is_active', 1);
        $collection->setOrder(['entity_id', 'path'], 'asc');

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($collection as $category) {
            $options[$category->getId()] =
                str_repeat(". ", max(0, ($category->getLevel() - 1) * 3)) . $category->getName();
        }

        return $options;
    }

    /**
     * @param bool $emptyOption
     * @return $this
     */
    public function setEmptyOption($emptyOption)
    {
        $this->emptyOption = $emptyOption;

        return $this;
    }
}
