<?php
/**
 * Hidro Forix Webdesign. 
 * Copyright (C) 2017  Hidro Le
 * 
 * This file included in Forix/QuoteLetter is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Forix\QuoteLetter\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface QuoteLetterRepositoryInterface
{


    /**
     * Save QuoteLetter
     * @param \Forix\QuoteLetter\Api\Data\QuoteLetterInterface $quoteLetter
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Forix\QuoteLetter\Api\Data\QuoteLetterInterface $quoteLetter
    );

    /**
     * Retrieve QuoteLetter
     * @param string $quoteletterId
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($quoteletterId);

    /**
     * Retrieve QuoteLetter matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete QuoteLetter
     * @param \Forix\QuoteLetter\Api\Data\QuoteLetterInterface $quoteLetter
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Forix\QuoteLetter\Api\Data\QuoteLetterInterface $quoteLetter
    );

    /**
     * Delete QuoteLetter by ID
     * @param string $quoteletterId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($quoteletterId);
}
