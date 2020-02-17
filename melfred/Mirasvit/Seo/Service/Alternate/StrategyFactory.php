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



namespace Mirasvit\Seo\Service\Alternate;

use Mirasvit\Seo\Api\Service\Alternate\StrategyFactoryInterface;

/**
 *@SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StrategyFactory implements StrategyFactoryInterface
{
    /**
     * @var \Mirasvit\Seo\Helper\Alternate\ProductStrategy
     */
    protected $productStrategy;

    /**
     * @var \Mirasvit\Seo\Helper\Alternate\CategoryStrategy
     */
    protected $categoryStrategy;

    /**
     * @var \Mirasvit\Seo\Helper\Alternate\CmsStrategyy
     */
    protected $cmsStrategy;

    /**
     * @var \Mirasvit\Seo\Helper\Alternate\DefaultStrategy
     */
    protected $defaultStrategy;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $page;

    /**
     * @param \Mirasvit\Seo\Service\Alternate\ProductStrategy $productStrategy
     * @param \Mirasvit\Seo\Service\Alternate\CategoryStrategy $categoryStrategy
     * @param \Mirasvit\Seo\Service\Alternate\CmsStrategy $cmsStrategy
     * @param \Mirasvit\Seo\Service\Alternate\DefaultPagesStrategy $defaultPagesStrategy,
     * @param \Mirasvit\Seo\Service\Alternate\DefaultStrategy $defaultStrategy
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Cms\Model\Page $page
     */
    public function __construct(
        \Mirasvit\Seo\Service\Alternate\ProductStrategy $productStrategy,
        \Mirasvit\Seo\Service\Alternate\CategoryStrategy $categoryStrategy,
        \Mirasvit\Seo\Service\Alternate\CmsStrategy $cmsStrategy,
        \Mirasvit\Seo\Service\Alternate\DefaultPagesStrategy $defaultPagesStrategy,
        \Mirasvit\Seo\Service\Alternate\DefaultStrategy $defaultStrategy,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Page $page
    ) {
        $this->productStrategy = $productStrategy;
        $this->categoryStrategy = $categoryStrategy;
        $this->cmsStrategy = $cmsStrategy;
        $this->defaultPagesStrategy = $defaultPagesStrategy;
        $this->defaultStrategy = $defaultStrategy;
        $this->request = $request;
        $this->registry = $registry;
        $this->page = $page;
    }


    /**
     * @return AbstractStrategy
     */
    public function create()
    {
        if ($this->request->getControllerName() == 'product'
            && $this->registry->registry('current_product')) {
                return $this->productStrategy;
        } elseif ($this->request->getControllerName() == 'category'
            && $this->registry->registry('current_category')) {
            return $this->categoryStrategy;
        } elseif ($this->request->getModuleName() == 'cms'
            && $this->page->getPageId()
            && $this->request->getActionName() != 'noRoute') {
                return $this->cmsStrategy;
        } elseif (in_array($this->request->getModuleName(), StrategyFactoryInterface::MODULE_NAME)) {
            return $this->defaultPagesStrategy;
        }

        return $this->defaultStrategy;
    }

}