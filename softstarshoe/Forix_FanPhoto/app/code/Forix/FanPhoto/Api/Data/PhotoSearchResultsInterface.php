<?php


namespace Forix\FanPhoto\Api\Data;

interface PhotoSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Photo list.
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface[]
     */
    public function getItems();

    /**
     * Set image_url list.
     * @param \Forix\FanPhoto\Api\Data\PhotoInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
