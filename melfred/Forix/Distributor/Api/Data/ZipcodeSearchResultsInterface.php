<?php


namespace Forix\Distributor\Api\Data;

interface ZipcodeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get zipcode list.
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface[]
     */
    public function getItems();

    /**
     * Set distributor_id list.
     * @param \Forix\Distributor\Api\Data\ZipcodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
