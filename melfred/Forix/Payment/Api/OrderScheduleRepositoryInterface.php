<?php


namespace Forix\Payment\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface OrderScheduleRepositoryInterface
{

    /**
     * Save OrderSchedule
     * @param \Forix\Payment\Api\Data\OrderScheduleInterface $orderSchedule
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\Payment\Api\Data\OrderScheduleInterface $orderSchedule
    );

    /**
     * Retrieve OrderSchedule
     * @param string $orderscheduleId
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($orderscheduleId);

    /**
     * Retrieve OrderSchedule matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\Payment\Api\Data\OrderScheduleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete OrderSchedule
     * @param \Forix\Payment\Api\Data\OrderScheduleInterface $orderSchedule
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\Payment\Api\Data\OrderScheduleInterface $orderSchedule
    );

    /**
     * Delete OrderSchedule by ID
     * @param string $orderscheduleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($orderscheduleId);
}
