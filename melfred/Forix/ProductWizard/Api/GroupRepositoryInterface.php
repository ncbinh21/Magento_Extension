<?php


namespace Forix\ProductWizard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroupRepositoryInterface
{


    /**
     * Save Group
     * @param \Forix\ProductWizard\Api\Data\GroupInterface $group
     * @return \Forix\ProductWizard\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\ProductWizard\Api\Data\GroupInterface $group
    );

    /**
     * Retrieve Group
     * @param string $groupId
     * @return \Forix\ProductWizard\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($groupId);

    /**
     * Retrieve Group matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\ProductWizard\Api\Data\GroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Group
     * @param \Forix\ProductWizard\Api\Data\GroupInterface $group
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\ProductWizard\Api\Data\GroupInterface $group
    );

    /**
     * Delete Group by ID
     * @param string $groupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groupId);
}
