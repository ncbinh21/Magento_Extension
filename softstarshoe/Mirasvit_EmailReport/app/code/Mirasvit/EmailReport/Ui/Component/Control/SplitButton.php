<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Ui\Component\Control;

use Magento\Ui\Component\Control\SplitButton as SplitButtonBase;

class SplitButton extends SplitButtonBase
{
    /**
     * {@inheritdoc}
     */
    protected function prepareOptionAttributes($option, $title, $classes, $disabled)
    {
        $attributes = [
            'id' => isset($option['id']) ? $this->getId() . '-' . $option['id'] : '',
            'title' => $title,
            'class' => join(' ', $classes),
            'onclick' => isset($option['onclick'])
                ? "setLocation('{$this->_urlBuilder->getUrl($option['onclick'])}')"
                : '',
            'style' => isset($option['style']) ? $option['style'] : '',
            'disabled' => $disabled,
        ];

        if (!empty($option['id_hard'])) {
            $attributes['id'] = $option['id_hard'];
        }

        if (isset($option['data_attribute'])) {
            $this->getDataAttributes($option['data_attribute'], $attributes);
        }

        return $attributes;
    }
}
