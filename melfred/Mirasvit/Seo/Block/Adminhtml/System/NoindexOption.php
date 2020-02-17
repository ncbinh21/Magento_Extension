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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Block\Adminhtml\System;

use Mirasvit\Seo\Model\Config as Config;

class NoindexOption extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @return array
     */
    protected function _getOptions()
    {
        return [
            Config::NOINDEX_FOLLOW => 'NOINDEX, FOLLOW',
            Config::INDEX_NOFOLLOW => 'INDEX, NOFOLLOW',
            Config::NOINDEX_NOFOLLOW => 'NOINDEX, NOFOLLOW',
        ];
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getOptions() as $groupId => $groupLabel) {
                $this->addOption($groupId, addslashes($groupLabel));
            }
        }

        return parent::_toHtml();
    }

    /**
     * @param string $optionValue
     * @return string
     */
    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getName().$this->getId().$optionValue));
    }

    /**
     * @param array      $option
     * @param bool|false $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected = false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' <%= option_extra_attrs.option_' . self::calcOptionHash($option['value']) . ' %>';
        }
        $html = '<option value="'.$this->escapeHtml($option['value']).'"'.$selectedHtml.'>'
            .$this->escapeHtml($option['label']).
            '</option>';

        return $html;
    }
}
