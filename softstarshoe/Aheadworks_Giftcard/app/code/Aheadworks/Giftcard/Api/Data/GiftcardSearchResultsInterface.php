<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for Gift Card search results
 * @api
 */
interface GiftcardSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get Gift Card list
     *
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardInterface[]
     */
    public function getItems();

    /**
     * Set Gift Card list
     *
     * @param \Aheadworks\Giftcard\Api\Data\GiftcardInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
