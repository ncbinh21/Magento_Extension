<?php

namespace Forix\CategoryCustom\Block\System;
/**
 * Class Multiselect
 * @package Forix\CategoryCustom\Block\System
 */
class Multiselect extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return parent::_getElementHtml($element) . "
        <script>
            require([
                'jquery',
                'chosen'
            ], function ($, chosen) {
                $('#" . $element->getId() . "').chosen({
                    width: '100%',
                    placeholder_text: '" .  __('Select Options') . "'
                });
            })
        </script>";
    }
}
