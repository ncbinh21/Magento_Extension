<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbyRoot\Plugin;

class Logo
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(\Magento\Framework\App\Request\Http $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Magento\Theme\Block\Html\Header\Logo $subject
     * @param \Closure $closure
     * @return bool
     */
    public function aroundIsHomePage(\Magento\Theme\Block\Html\Header\Logo $subject, \Closure $closure)
    {
        if ($this->request->getRouteName() == 'amshopby') {
            return false;
        }
        return $closure();
    }
}
