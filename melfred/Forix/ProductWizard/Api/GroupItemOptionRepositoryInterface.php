<?php


namespace Forix\ProductWizard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroupItemOptionRepositoryInterface
{


    /**
     * Save Group_Item_Option
     * @param \Forix\ProductWizard\Api\Data\GroupItemOptionInterface $groupItemOption
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\ProductWizard\Api\Data\GroupItemOptionInterface $groupItemOption
    );

    /**
     * Retrieve Group_Item_Option
     * @param string $groupItemOptionId
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($groupItemOptionId);

    /**
     * Retrieve Group_Item_Option matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\ProductWizard\Api\Data\GroupItemOptionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Group_Item_Option
     * @param \Forix\ProductWizard\Api\Data\GroupItemOptionInterface $groupItemOption
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\ProductWizard\Api\Data\GroupItemOptionInterface $groupItemOption
    );

    /**
     * Delete Group_Item_Option by ID
     * @param string $groupItemOptionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groupItemOptionId);
}
