<?php


namespace Forix\ProductWizard\Api\Data;

interface GroupItemSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Group_Item list.
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Forix\ProductWizard\Api\Data\GroupItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
