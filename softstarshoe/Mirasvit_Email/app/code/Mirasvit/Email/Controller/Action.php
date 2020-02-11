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
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Email\Controller;

use Magento\Framework\App\Action\Context;
use Mirasvit\Email\Helper\Frontend;

abstract class Action extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Frontend
     */
    protected $frontendHelper;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context  $context
     * @param Frontend $frontendHelper
     */
    public function __construct(
        Context $context,
        Frontend $frontendHelper
    ) {
        $this->context = $context;
        $this->frontendHelper = $frontendHelper;

        parent::__construct($context);
    }

    /**
     * Return url @todo refactoring
     *
     * @param string $url
     * @param bool   $full
     * @return string
     */
    protected function _getUrl($url, $full = false)
    {
        $params = [];
        foreach ($this->getRequest()->getParams() as $key => $value) {
            if (strpos($key, 'utm_') !== false) {
                $params[$key] = $value;
            }
        }

        if ($full) {
            $url = $this->context->getUrl()->getUrl($url, ['_query' => $params]);
        } else {
            $query = http_build_query($params);

            if ($query) {
                if (strpos($url, '?') !== false) {
                    $url .= '&' . $query;
                } else {
                    $url .= '?' . $query;
                }
            }
        }

        // Place hash to the end of URL
        if (($hashPos = strpos($url, '#')) && strpos($url, '?') > $hashPos) {
            $fragment = substr($url, $hashPos, strpos($url, '?') - $hashPos);
            $url = str_replace($fragment, '', $url).$fragment;
        }

        // tmp fix. Redirection allowed only within current domain name (currenst store base url)
        $currentHost = parse_url($this->_url->getBaseUrl(), PHP_URL_HOST);
        $urlHost = parse_url($url, PHP_URL_HOST);
        if ($currentHost !== $urlHost) {
            $url = str_replace($urlHost, $currentHost, $url);
        }

        return $url;
    }
}
