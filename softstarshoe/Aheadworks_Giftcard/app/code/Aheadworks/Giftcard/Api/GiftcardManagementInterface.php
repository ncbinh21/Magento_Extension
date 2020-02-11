<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Api;

/**
 * Interface GiftcardManagementInterface
 * @api
 */
interface GiftcardManagementInterface
{
    /**
     * Send Gift Card by codes
     *
     * @param string[]|string $giftcardCodes
     * @param bool $validateDeliveryDate
     * @param int|null $storeId
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardInterface[]
     */
    public function sendGiftcardByCode($giftcardCodes, $validateDeliveryDate = true, $storeId = null);

    /**
     * Retrieve Gift Card codes by customer email
     *
     * @param string|null $customerEmail
     * @param int|null $cartId
     * @param int|null $storeId
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardInterface[]
     */
    public function getCustomerGiftcards($customerEmail = null, $cartId = null, $storeId = null);

    /**
     * Add comment to order with Gift Card
     *
     * @param \Aheadworks\Giftcard\Api\Data\GiftcardInterface $giftcard
     * @param string $comment
     * @param int $visibleOnFront
     * @param int $customerNotified
     * @return bool
     */
    public function addCommentToGiftcardOrder($giftcard, $comment, $visibleOnFront = 0, $customerNotified = 0);

    /**
     * Generate Gift Card code
     *
     * @param int|null $websiteId
     * @param \Aheadworks\Giftcard\Api\Data\CodeGenerationSettingsInterface|null $codeGenerationSettings
     * @return string[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateCodes($websiteId = null, $codeGenerationSettings = null);

    /**
     * Import codes
     *
     * @param mixed $codesRawData
     * @return \Aheadworks\Giftcard\Api\Data\GiftcardInterface[]
     * @throws \Aheadworks\Giftcard\Api\Exception\ImportValidatorExceptionInterface
     */
    public function importCodes($codesRawData);
}
