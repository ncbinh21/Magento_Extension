<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Giftcard CRUD interface
 * @api
 */
interface GiftcardRepositoryInterface
{
    /**
     * Save giftcard
     *
     * @param \Aheadworks\Giftcard\Api\Data\GiftcardInterface $giftcard
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Giftcard\Api\Data\GiftcardInterface $giftcard);

    /**
     * Retrieve Gift Card by id
     *
     * @param int $giftcardId
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($giftcardId);

    /**
     * Retrieve Gift Card by code
     *
     * @param string $giftcardCode
     * @param int|null $websiteId
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByCode($giftcardCode, $websiteId = null);

    /**
     * Retrieve giftcards matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete giftcard
     *
     * @param \Aheadworks\Giftcard\Api\Data\GiftcardInterface $giftcard
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Giftcard\Api\Data\GiftcardInterface $giftcard);

    /**
     * Delete giftcard by ID
     *
     * @param int $giftcardId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($giftcardId);
}
