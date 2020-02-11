<?php
namespace Forix\Base\Block\Header;

abstract class SocialMarkup extends \Forix\Base\Block\Header
{
    /**
     *
     * {@inheritDoc}
     */
    protected function _toHtml()
    {
        return $this->getMarkupHtml();
    }
}
