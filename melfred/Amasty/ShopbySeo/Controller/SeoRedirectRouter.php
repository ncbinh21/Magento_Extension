<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Controller;

/**
 * Routher has sort order 15 and run before catalog url rewrite
 */
class SeoRedirectRouter extends Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool|\Magento\Framework\App\ActionInterface|void
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (strpos($request->getRequestString(), '?') !== false) {
            return $this->createSeoRedirect($request);
        }

        return false;
    }
}
