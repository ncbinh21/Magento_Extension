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



namespace Mirasvit\Seo\Model\Plugin;

class Pager
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry               $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Theme\Block\Html\Pager $subject
     * @param string                          $url
     *
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPageUrl($subject, $url)
    {
        $url = $this->preparePageUrl($url);

        return $url;
    }

    /**
     * @param string $url
     *
     * @return string $url
     */
    protected function preparePageUrl($url)
    {
        if (preg_match('/p=1/', $url)) {
            $url = trim(str_replace('&amp;', '&', $url));
        } else {
            return $url;
        }

        if (preg_match('/\?p=1$/', $url)) {
            $url = str_replace('?p=1', '', $url);
        } elseif (preg_match('/&p=1$/', $url)) {
            $url = str_replace('&p=1', '', $url);
        } elseif (preg_match('/\?p=1&/', $url) || preg_match('/&p=1&/', $url)) {
            $url = str_replace('p=1&', '', $url);
        }

        return $url;
    }
}
