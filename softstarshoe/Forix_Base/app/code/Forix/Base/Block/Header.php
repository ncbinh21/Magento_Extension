<?php

namespace Forix\Base\Block;

abstract class Header extends \Magento\Framework\View\Element\Template
{
    /**
     * @return string (HTML)
     */
    abstract protected function getMarkupHtml();
}
