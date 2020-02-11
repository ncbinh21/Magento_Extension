<?php


namespace Forix\FanPhoto\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PhotoRepositoryInterface
{


    /**
     * Save Photo
     * @param \Forix\FanPhoto\Api\Data\PhotoInterface $photo
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\FanPhoto\Api\Data\PhotoInterface $photo
    );

    /**
     * Retrieve Photo
     * @param string $photoId
     * @return \Forix\FanPhoto\Api\Data\PhotoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($photoId);

    /**
     * Retrieve Photo matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\FanPhoto\Api\Data\PhotoSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Photo
     * @param \Forix\FanPhoto\Api\Data\PhotoInterface $photo
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\FanPhoto\Api\Data\PhotoInterface $photo
    );

    /**
     * Delete Photo by ID
     * @param string $photoId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($photoId);
}
