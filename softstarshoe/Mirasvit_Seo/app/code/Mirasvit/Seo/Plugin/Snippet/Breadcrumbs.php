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



namespace Mirasvit\Seo\Plugin\Snippet;

use Mirasvit\Seo\Model\Config;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Breadcrumbs
{
    /**
     * @param Registry $registry
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Registry $registry,
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->registry = $registry;
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @param Magento\Theme\Block\Html\Breadcrumbs $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToHtml($subject, $result)
    {
        $this->parseBreadcrumbs($result);

        return $result;
    }

    public function parseBreadcrumbs($html) {
        $breadcrumbs = $this->config->getBreadcrumbs($this->storeManager->getStore()->getId());
        if (!$breadcrumbs) {
            return false;
        }

        preg_match_all('/\\<li(.*?)\\/li\\>/ims', $html, $liTagResult);
        if (isset($liTagResult[1]) && count($liTagResult[1]) > 1) {
            preg_match_all('/\\<a(.*?)\\/a\\>/ims', implode(' ', $liTagResult[1]), $aTagResult);
            if (isset($aTagResult[0])) {
                preg_match_all('/href="(.*?)"/ims', implode(' ', $aTagResult[0]), $links);
                preg_match_all('/\\>(.*?)\\<\\/a\\>/ims', implode(' ', $aTagResult[0]), $title);
                $breadcrumbsArray = [];
                foreach ($title[1] as $key => $value) {
                    $breadcrumbsArray[$links[1][$key]] = $value;
                }
                $this->registry->register('m__snipets_breadcrumbs', $breadcrumbsArray);
            }
        }
    }
}
