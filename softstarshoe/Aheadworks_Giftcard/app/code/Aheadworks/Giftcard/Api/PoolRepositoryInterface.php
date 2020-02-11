<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Pool CRUD interface
 * @api
 */
interface PoolRepositoryInterface
{
    /**
     * Save pool
     *
     * @param \Aheadworks\Giftcard\Api\Data\PoolInterface $pool
     * @return \Aheadworks\Giftcard\Api\Data\PoolInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Giftcard\Api\Data\PoolInterface $pool);

    /**
     * Retrieve pool by id
     *
     * @param int $poolId
     * @return \Aheadworks\Giftcard\Api\Data\PoolInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($poolId);

    /**
     * Retrieve pools matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Giftcard\Api\Data\PoolSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete pool
     *
     * @param \Aheadworks\Giftcard\Api\Data\PoolInterface $pool
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Giftcard\Api\Data\PoolInterface $pool);

    /**
     * Delete pool by ID
     *
     * @param int $poolId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($poolId);
}
