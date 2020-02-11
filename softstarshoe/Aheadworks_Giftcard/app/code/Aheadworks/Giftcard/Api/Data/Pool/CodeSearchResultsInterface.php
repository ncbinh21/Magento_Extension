<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api\Data\Pool;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for pool search results
 * @api
 */
interface CodeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get pool code list
     *
     * @return \Aheadworks\Giftcard\Api\Data\Pool\CodeInterface[]
     */
    public function getItems();

    /**
     * Set pool code list
     *
     * @param \Aheadworks\Giftcard\Api\Data\Pool\CodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
