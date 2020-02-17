<?php


namespace Forix\ProductWizard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroupItemRepositoryInterface
{


    /**
     * Save Group_Item
     * @param \Forix\ProductWizard\Api\Data\GroupItemInterface $groupItem
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\ProductWizard\Api\Data\GroupItemInterface $groupItem
    );

    /**
     * Retrieve Group_Item
     * @param string $groupItemId
     * @return \Forix\ProductWizard\Api\Data\GroupItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($groupItemId);

    /**
     * Retrieve Group_Item matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\ProductWizard\Api\Data\GroupItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Group_Item
     * @param \Forix\ProductWizard\Api\Data\GroupItemInterface $groupItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\ProductWizard\Api\Data\GroupItemInterface $groupItem
    );

    /**
     * Delete Group_Item by ID
     * @param string $groupItemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groupItemId);
}
