<?php


namespace Forix\Media\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface VideoRepositoryInterface
{


    /**
     * Save Video
     * @param \Forix\Media\Api\Data\VideoInterface $video
     * @return \Forix\Media\Api\Data\VideoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\Media\Api\Data\VideoInterface $video
    );

    /**
     * Retrieve Video
     * @param string $videoId
     * @return \Forix\Media\Api\Data\VideoInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($videoId);

    /**
     * Retrieve Video matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\Media\Api\Data\VideoSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Video
     * @param \Forix\Media\Api\Data\VideoInterface $video
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\Media\Api\Data\VideoInterface $video
    );

    /**
     * Delete Video by ID
     * @param string $videoId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($videoId);
}
