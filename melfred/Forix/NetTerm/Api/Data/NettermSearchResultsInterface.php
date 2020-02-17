<?php


namespace Forix\NetTerm\Api\Data;

interface NettermSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get netterm list.
     * @return \Forix\NetTerm\Api\Data\NettermInterface[]
     */
    public function getItems();

    /**
     * Set business list.
     * @param \Forix\NetTerm\Api\Data\NettermInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
