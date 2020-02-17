<?php


namespace Forix\ProductWizard\Api\Data;

interface GroupItemOptionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Group_Item_Option list.
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Forix\ProductWizard\Api\Data\GroupItemOptionInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
