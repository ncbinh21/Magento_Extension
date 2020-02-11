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

namespace Forix\QuoteLetter\Api\Data;

interface QuoteLetterSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get QuoteLetter list.
     * @return \Forix\QuoteLetter\Api\Data\QuoteLetterInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Forix\QuoteLetter\Api\Data\QuoteLetterInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
