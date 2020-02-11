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


namespace Mirasvit\Email\Model\Variable;

use Magento\Framework\UrlInterface;
use Mirasvit\EmailDesigner\Model\Variable\Context;

class Url
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param UrlInterface $urlBuilder
     * @param Context      $context
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Context $context
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getRestoreCartUrl()
    {
        return $this->getUrl('email/action/restoreCart');
    }

    /**
     * Resume customer's session, before redirecting to target URL.
     *
     * @param string|null $to - target URL
     *
     * @return string
     */
    public function getResumeUrl($to = null)
    {
        $params = [];
        if ($to) {
            $params['to'] = base64_encode($to);
        }

        return $this->getUrl('email/action/resume', $params);
    }

    /**
     * @return string
     */
    public function getCheckoutUrl()
    {
        return $this->getUrl('email/action/checkout');
    }

    /**
     * @return string
     */
    public function getViewInBrowserUrl()
    {
        return $this->getUrl('email/action/view');
    }

    /**
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return $this->getUrl('email/action/unsubscribe');
    }

    /**
     * @return string
     */
    public function getUnsubscribeAllUrl()
    {
        return $this->getUrl('email/action/unsubscribeAll');
    }

    /**
     * @param string $route
     * @param array  $params
     * @return string
     */
    protected function getUrl($route, $params = [])
    {
        /** @var \Magento\Store\Model\Store $store */
        $store = $this->context->getStore();

        if ($this->context->getQueue() && $store) {
            $hash = $this->context->getQueue()->getUniqHash();

            $params = array_merge(['hash' => $hash], $params);

            return $store->getBaseUrl() . $route . '?' . http_build_query($params);
        } else {
            return '';
        }
    }
}
