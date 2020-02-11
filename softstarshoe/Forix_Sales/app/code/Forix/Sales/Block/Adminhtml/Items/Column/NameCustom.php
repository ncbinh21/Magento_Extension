<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Sales\Block\Adminhtml\Items\Column;

/**
 * Sales Order items name column renderer
 *
 * @api
 * @since 100.0.2
 */
class NameCustom extends \Magento\Sales\Block\Adminhtml\Items\Column\Name
{
    /**
     * Add line breaks and truncate value
     *
     * @param string $value
     * @return array
     */
    public function getFormattedOption($value)
    {
        $remainder = '';
        $value = $this->truncateString($value, 100, '', $remainder);
        $result = ['value' => nl2br($value), 'remainder' => nl2br($remainder)];

        return $result;
    }
}
