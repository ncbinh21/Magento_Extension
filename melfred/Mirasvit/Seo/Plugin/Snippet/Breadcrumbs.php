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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Plugin\Snippet;

use Mirasvit\Seo\Model\Config;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Api\Service\PageDetectorInterface;

class Breadcrumbs
{
    /**
     * @var array
     */
    protected $breadcrumbsArray = [];

    /**
     * Breadcrumbs constructor.
     * @param Registry $registry
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param PageDetectorInterface $pageDetector
     */
    public function __construct(
        Registry $registry,
        Config $config,
        StoreManagerInterface $storeManager,
        PageDetectorInterface $pageDetector
    ) {
        $this->registry = $registry;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->pageDetector = $pageDetector;
    }

    /**
     * @param \Magento\Theme\Block\Html\Breadcrumbs $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToHtml($subject, $result)
    {
        $this->parseBreadcrumbs($result);

        return $result;
    }

    /**
     * @param string $html
     * @return bool
     */
    public function parseBreadcrumbs($html) {
        $breadcrumbs = $this->config->getBreadcrumbs($this->storeManager->getStore()->getId());
        if (!$breadcrumbs) {
            return false;
        }

        preg_match_all('/\\<li(.*?)\\/li\\>/ims', $html, $liTagResult);
        if (isset($liTagResult[0]) && count($liTagResult[0]) > 1) {
            foreach ($liTagResult[0] as $line) {
                $this->setBreadcrumbsData($line);
            }

            if ($this->breadcrumbsArray) {
                $this->registry->register('m__snipets_breadcrumbs', $this->breadcrumbsArray, true);
            }
        }
    }

    /**
     * @param string $line
     * @return void
     */
    protected function setBreadcrumbsData($line)
    {
        if (strpos($line, '</a>') !== false) {
            $this->setBreadcrumbsFromLineWithLinks($line);
        } elseif ($this->pageDetector->isCategory()) {
            $this->setBreadcrumbsFromLineWithoutLinks($line);
        }
    }

    /**
     * @param string $line
     * @return void
     */
    protected function setBreadcrumbsFromLineWithLinks($line)
    {
        preg_match_all('/\\<a(.*?)\\/a\\>/ims', $line, $aTagResult);
        if (isset($aTagResult[0])) {
            preg_match_all('/href="(.*?)"/ims', implode(' ', $aTagResult[0]), $links);
            preg_match_all('/\\>(.*?)\\<\\/a\\>/ims', implode(' ', $aTagResult[0]), $title);
            if (isset($links[1][0]) && isset($title[1][0])) {
                $this->breadcrumbsArray[$links[1][0]] = $title[1][0];
            }
        }
    }

    /**
     * @param string $line
     * @return void
     */
    protected function setBreadcrumbsFromLineWithoutLinks($line)
    {
        $this->breadcrumbsArray[$this->registry->registry('current_category')->getUrl()] = strip_tags($line);
    }
}
