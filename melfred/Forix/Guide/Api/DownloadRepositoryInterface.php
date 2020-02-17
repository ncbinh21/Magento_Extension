<?php


namespace Forix\Guide\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface DownloadRepositoryInterface
{

    /**
     * Save download
     * @param \Forix\Guide\Api\Data\DownloadInterface $download
     * @return \Forix\Guide\Api\Data\DownloadInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\Guide\Api\Data\DownloadInterface $download
    );

    /**
     * Retrieve download
     * @param string $downloadId
     * @return \Forix\Guide\Api\Data\DownloadInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($downloadId);

    /**
     * Retrieve download matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\Guide\Api\Data\DownloadSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete download
     * @param \Forix\Guide\Api\Data\DownloadInterface $download
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\Guide\Api\Data\DownloadInterface $download
    );

    /**
     * Delete download by ID
     * @param string $downloadId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($downloadId);
}
