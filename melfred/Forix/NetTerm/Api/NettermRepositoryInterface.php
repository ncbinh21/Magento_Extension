<?php


namespace Forix\NetTerm\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface NettermRepositoryInterface
{

    /**
     * Save netterm
     * @param \Forix\NetTerm\Api\Data\NettermInterface $netterm
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\NetTerm\Api\Data\NettermInterface $netterm
    );

    /**
     * Retrieve netterm
     * @param string $nettermId
     * @return \Forix\NetTerm\Api\Data\NettermInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($nettermId);

    /**
     * Retrieve netterm matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\NetTerm\Api\Data\NettermSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete netterm
     * @param \Forix\NetTerm\Api\Data\NettermInterface $netterm
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\NetTerm\Api\Data\NettermInterface $netterm
    );

    /**
     * Delete netterm by ID
     * @param string $nettermId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($nettermId);
}
