<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Config\Source;

use Magento\Customer\Model\Group;

class CustomerGroup extends \Magento\Customer\Model\Config\Source\Group
{
    public function toOptionArray()
    {
        $result = parent::toOptionArray();

        array_shift($result);

        array_unshift($result, [
            'value' => Group::NOT_LOGGED_IN_ID,
            'label' => __('NOT LOGGED IN')
        ]);

        return  $result;
    }
}
