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



namespace Mirasvit\Seo\Block\Html;

class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Seo\Helper\Data                        $seoData
     * @param \Magento\Framework\Module\Manager                $moduleManager
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->seoData = $seoData;
        $this->moduleManager = $moduleManager;
        $this->registry = $registry;
        $this->request = $context->getRequest();
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @var string
     */
    protected $fullActionCode;

    /**
     * @return string
     */
    protected function getFullActionCode()
    {
        if (!$this->fullActionCode) {
            $request = $this->request;
            $this->fullActionCode =
                $request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName();
        }

        return $this->fullActionCode;
    }

    /**
     * @param string $page
     * @return string
     */
    public function getPageUrl($page)
    {
        if ($page == 1) {
            $params = $this->request->getQuery();
            if (isset($params['p'])) {
                unset($params['p']);
            }

            $urlParams['_use_rewrite'] = true;
            $urlParams['_escape'] = true;
            $urlParams['_query'] = $params;

            return $this->getUrl('*/*/*', $urlParams);
        } else {
            return $this->getPagerUrl([$this->getPageVarName() => $page]);
        }
    }

    /**
     * @param int $limit
     * @return string
     */
    public function getLimitUrl($limit)
    {
        if ($this->getFullActionCode() == 'review_product_list') {
            return $this->getSeoReviewUrl(false, $limit);
        } else {
            return parent::getLimitUrl($limit);
        }
    }
}
