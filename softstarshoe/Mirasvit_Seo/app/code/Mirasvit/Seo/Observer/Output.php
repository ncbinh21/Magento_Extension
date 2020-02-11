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



namespace Mirasvit\Seo\Observer;

use Magento\Framework\Event\ObserverInterface;

class Output implements ObserverInterface
{
    /**
     * @var \Mirasvit\Seo\Model\Paging
     */
    protected $paging;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Mirasvit\Seo\Model\Paging  $paging
     * @param \Mirasvit\Seo\Model\Config  $config
     * @param \Mirasvit\Seo\Helper\Data   $seoData
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Mirasvit\Seo\Model\Paging $paging,
        \Mirasvit\Seo\Model\Config $config,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\Registry $registry
    ) {
        $this->paging = $paging;
        $this->config = $config;
        $this->seoData = $seoData;
        $this->registry = $registry;
    }

    /**
     * Add links prev/next to the Meta tags.
     *
     * @return void
     */
    public function setupPagingMeta()
    {
        if ($this->config->isPagingPrevNextEnabled()
            && !$this->seoData->isIgnoredActions()
            && $this->isCategory()) {
                $this->paging->createLinks();
        }
    }

    /**
     *  Check if category page
     *
     * @return bool
     */
    protected function isCategory()
    {
        if (($category = $this->registry->registry('current_category'))
            && $category->getId() && !$this->registry->registry('current_product')) {
                return true;
        }

        return false;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->setupPagingMeta();
    }
}
