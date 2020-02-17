<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyRoot
 */

namespace Amasty\ShopbyRoot\Plugin\Theme\Block\Html\Header;

class Logo
{
    const SHOPBY_ROUTE_NAME = 'amshopby';

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * Logo constructor.
     * @param \Magento\Framework\App\Request\Http $request
     */
    public function __construct(\Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Magento\Theme\Block\Html\Header\Logo $subject
     * @param \Closure $closure
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function aroundIsHomePage(\Magento\Theme\Block\Html\Header\Logo $subject, \Closure $closure)
    {
        if ($this->request->getRouteName() == self::SHOPBY_ROUTE_NAME) {
            return false;
        }
        return $closure();
    }
}
