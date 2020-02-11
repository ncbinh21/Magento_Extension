<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Block\Customer;

use Magento\Framework\View\Element\Template;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Block\Customer
 */
class Giftcard extends Template
{
    /**
     * Retrieve check Gift Card code url
     *
     * @return string
     */
    public function getCheckCodeUrl()
    {
        return $this->getUrl('awgiftcard/card/checkCode');
    }
}
