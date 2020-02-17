<?php


namespace Forix\Media\Api\Data;

interface VideoSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Video list.
     * @return \Forix\Media\Api\Data\VideoInterface[]
     */
    public function getItems();

    /**
     * Set title list.
     * @param \Forix\Media\Api\Data\VideoInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
