<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for pool search results
 * @api
 */
interface PoolSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pool list
     *
     * @return \Aheadworks\Giftcard\Api\Data\PoolInterface[]
     */
    public function getItems();

    /**
     * Set pool list
     *
     * @param \Aheadworks\Giftcard\Api\Data\PoolInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
