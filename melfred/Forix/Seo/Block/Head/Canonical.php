<?php

namespace Forix\Seo\Block\Head;

use Magento\Framework\View\Element\Template;

abstract class Canonical extends Template
{

    abstract protected function getCanonicalHtml();

    protected function _toHtml()
    {
        if (false != $this->getTemplate()) {
            return parent::_toHtml();
        }
        return $this->getCanonicalHtml();
    }
}