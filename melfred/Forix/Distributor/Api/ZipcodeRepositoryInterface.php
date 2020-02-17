<?php


namespace Forix\Distributor\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ZipcodeRepositoryInterface
{

    /**
     * Save zipcode
     * @param \Forix\Distributor\Api\Data\ZipcodeInterface $zipcode
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\Distributor\Api\Data\ZipcodeInterface $zipcode
    );

    /**
     * Retrieve zipcode
     * @param string $zipcodeId
     * @return \Forix\Distributor\Api\Data\ZipcodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($zipcodeId);

    /**
     * Retrieve zipcode matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\Distributor\Api\Data\ZipcodeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete zipcode
     * @param \Forix\Distributor\Api\Data\ZipcodeInterface $zipcode
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\Distributor\Api\Data\ZipcodeInterface $zipcode
    );

    /**
     * Delete zipcode by ID
     * @param string $zipcodeId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($zipcodeId);
}
