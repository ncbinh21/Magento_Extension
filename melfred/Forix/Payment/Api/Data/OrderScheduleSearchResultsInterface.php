<?php


namespace Forix\Payment\Api\Data;

interface OrderScheduleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get OrderSchedule list.
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface[]
     */
    public function getItems();

    /**
     * Set sales_order_no list.
     * @param \Forix\Payment\Api\Data\OrderScheduleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
