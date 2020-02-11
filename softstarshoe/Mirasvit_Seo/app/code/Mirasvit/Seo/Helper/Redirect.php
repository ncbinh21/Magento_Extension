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



namespace Mirasvit\Seo\Helper;

use Mirasvit\Seo\Model\Config as Config;

class Redirect extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlManager;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;

    /**
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Mirasvit\Seo\Model\Config                        $config
     * @param \Magento\Framework\UrlInterface                   $urlManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\App\ActionFlag                 $actionFlag
     * @param \Magento\Customer\Model\Session                   $customerSession
     */
    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Magento\Framework\UrlInterface $urlManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\App\ActionFlag $actionFlag,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->config = $config;
        $this->urlManager = $urlManager;
        $this->redirect = $redirect;
        $this->actionFlag = $actionFlag;
        $this->customerSession = $customerSession;
    }

    /**
     * @param string $url
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getUrlWithCorrectEndSlash($url)
    {
        if (strpos($url, '?') !== false) {
            return $url;
        }
        $extension = substr(strrchr($url, '.'), 1);

        if (substr($url, -1) != '/' && $this->config->getTrailingSlash() == Config::TRAILING_SLASH) {
            if (!in_array($extension, ['html', 'htm', 'php', 'xml', 'rss'])) {
                $url .= '/';
                if ($_SERVER['QUERY_STRING']) {
                    $url .= '?'.$_SERVER['QUERY_STRING'];
                }
            }
        } elseif ($url != '/' && substr($url, -1) == '/' &&
                $this->config->getTrailingSlash() == Config::NO_TRAILING_SLASH) {
            $url = rtrim($url, '/');
            if ($_SERVER['QUERY_STRING']) {
                $url .= '?'.$_SERVER['QUERY_STRING'];
            }
        }

        if (substr($url, -6) == '.html/') {
            $url = rtrim($url, '/');
        }

        return $url;
    }


    /**
     * @param string $response
     * @param string $url
     * @return bool
     */
    public function redirect($response, $url)
    {
        $currentUrl = $this->urlManager->getCurrentUrl();
        if (strpos($currentUrl, 'customer/account')
            && strpos($currentUrl, 'customer/account/create') === false) {
                return false;
        }

        $this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
        // don't use $this->redirect->redirect($response, $url); - create incorrect category redirect
        $response->setRedirect($url, 301)->sendResponse();

        return true;
    }

    /**
     * @param string $urlFrom
     * @param string $urlTo
     * @param bool $redirectOnlyErrorPage
     * @return bool
     */
    public function checkRedirectPattern($urlFrom, $urlTo, $redirectOnlyErrorPage = false) {
        if ($urlFrom == '/*' && $urlTo == '/' && $redirectOnlyErrorPage) {
            return false;
        }
        $urlFrom = preg_quote($urlFrom, '/');
        $urlFrom = str_replace('\*', '(.*?)', $urlFrom);
        $pattern = '/' . $urlFrom . '$/ims';

        if (preg_match($pattern, $urlTo)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $urlTo
     * @return bool
     */
    public function checkForLoop($urlTo) {
        $redirectArray = $this->getRedirectedUrls();
        if (in_array($urlTo, $redirectArray)) {
            $this->customerSession->unsetData('redirects_array');
            return true;
        }

        return false;
    }

    /**
     * @param string $currentUrl
     */
    public function setFlag($currentUrl) {
        $redirectsArray = $this->getRedirectedUrls();
        array_push($redirectsArray, $currentUrl);
        $this->customerSession->setData('redirects_array', $redirectsArray);
    }

    public function unsetFlag() {
        $this->customerSession->unsetData('redirects_array');
    }

    /**
     * @return array
     */
    public function getRedirectedUrls() {
        $redirectArray = $this->customerSession->getData('redirects_array');
        return $redirectArray ? $redirectArray : array() ;
    }
}
