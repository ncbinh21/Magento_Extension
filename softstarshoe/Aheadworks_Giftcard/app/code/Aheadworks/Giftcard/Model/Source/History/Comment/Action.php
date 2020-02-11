<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Source\History\Comment;

/**
 * Class Action
 *
 * @package Aheadworks\Giftcard\Model\Source\History
 */
class Action
{
    /**#@+
     * Comment action values
     */
    const BY_ADMIN = 1;
    const CREATED_BY_ORDER = 2;
    const APPLIED_TO_ORDER = 3;
    const REFUND_GIFTCARD = 4;
    const REIMBURSED_FOR_CANCELLED_ORDER = 5;
    const REIMBURSED_FOR_REFUNDED_ORDER = 6;
    const EXPIRED = 7;
    const DELIVERY_DATE_EMAIL_STATUS = 8;
    const TYPE_CHANGED = 9;
    /**#@-*/
}
