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

class Breadcrumbs extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Seo\Helper\Data             $seoData
     * @param \Magento\Framework\Registry           $registry
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->seoData = $seoData;
        $this->registry = $registry;
        $this->context = $context;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCategory()
    {
        if ($product = $this->registry->registry('current_product')) {
            return $this->seoData->getProductSeoCategory($product);
        }

        return $this->registry->registry('current_category');
    }
}
