<?php


namespace Forix\ProductWizard\Api\Data;

interface GroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Group list.
     * @return \Forix\ProductWizard\Api\Data\GroupInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Forix\ProductWizard\Api\Data\GroupInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
