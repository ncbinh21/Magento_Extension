<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\NetTerm\Model\Config\Source;

/**
 * Class Yesno.
 */
class Yesno implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {inheritdoc}
     */
    public function toOptionArray()
    {
        return [['value' => true, 'label' => __('Yes')], ['value' => false, 'label' => __('No')]];
    }
}
