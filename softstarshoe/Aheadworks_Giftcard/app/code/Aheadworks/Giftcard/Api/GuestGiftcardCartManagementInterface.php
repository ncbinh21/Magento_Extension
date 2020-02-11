<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\CouldNotDeleteException;

/**
 * Interface GuestGiftcardCartManagementInterface
 * @api
 */
interface GuestGiftcardCartManagementInterface
{
    /**
     * Retrieve information for Gift Card codes in guest cart
     *
     * @param string $cartId
     * @return boolean
     * @throws NoSuchEntityException Cart $cartId doesn't contain products
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function get($cartId);

    /**
     * Add Gift Card code to guest cart
     *
     * @param string $cartId
     * @param string $giftcardCode
     * @return boolean
     * @throws CouldNotSaveException The specified Gift Card code not be added
     * @throws NoSuchEntityException Cart $cartId doesn't contain products
     * @throws NoSuchEntityException The specified Gift Card code is not valid
     * @throws LocalizedException The specified Gift Card code deactivated
     * @throws LocalizedException The specified Gift Card code expired
     * @throws LocalizedException The specified Gift Card code used
     */
    public function set($cartId, $giftcardCode);

    /**
     * Delete Gift Card code from guest cart
     *
     * @param string $cartId
     * @param string $giftcardCode
     * @return boolean
     * @throws NoSuchEntityException Cart $cartId doesn't contain products
     * @throws NoSuchEntityException The specified Gift Card code is not valid
     * @throws CouldNotDeleteException The specified Gift Card code could not be deleted
     */
    public function remove($cartId, $giftcardCode);
}
