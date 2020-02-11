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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Cookie;

use Magento\Framework\Stdlib\CookieManagerInterface;
use Mirasvit\Seo\Model\Config as Config;

class Cookie extends \Magento\Framework\DataObject
{
    /**
     * @param CookieManagerInterface                                  $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory  $cookieMetadataFactory
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
    }

    /**
     * Add/Remove Cookie
     *
     * @return string|bool
     */
    public function applyCookie()
    {
        $result = 'Something went wrong.';
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()->setPath('/');
        if ($this->isCookieExist()) {
            $this->cookieManager->deleteCookie(Config::BYPASS_COOKIE, $metadata);
            $result = false;
        } else {
            $this->cookieManager->setPublicCookie(Config::BYPASS_COOKIE, '1', $metadata);
            $result = true;
        }

        return $result;
    }

    /**
     * Check if cookie exist
     *
     * @return bool
     */
    public function isCookieExist()
    {
        if ($this->cookieManager->getCookie(Config::BYPASS_COOKIE)) {
            return true;
        }

        return false;
    }

    /**
     * Button label
     *
     * @return string
     */
    public function getButtonLabel()
    {
        if ($this->isCookieExist()) {
            return Config::COOKIE_DEL_BUTTON;
        }

        return Config::COOKIE_ADD_BUTTON;
    }

}
