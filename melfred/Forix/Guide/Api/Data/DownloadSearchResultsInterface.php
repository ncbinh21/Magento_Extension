<?php


namespace Forix\Guide\Api\Data;

interface DownloadSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get download list.
     * @return \Forix\Guide\Api\Data\DownloadInterface[]
     */
    public function getItems();

    /**
     * Set customer_id list.
     * @param \Forix\Guide\Api\Data\DownloadInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
