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



namespace Mirasvit\Seo\Block\Snippets\BlogMx;

use Mirasvit\Seo\Traits\SnippetsTrait;

class CategorySnippets extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Seo\Api\Data\BlogMx\CategoryInterface
     */
    protected $blogCategory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Mirasvit\Seo\Api\Data\BlogMx\CategoryInterface $blogCategory
     * @param \Magento\Framework\View\Element\Template\Contex $context
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $data
     */
    public function __construct(
        \Mirasvit\Seo\Api\Config\BlogMxInterface $blogMxConfig,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->blogMxConfig = $blogMxConfig;
        $this->objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @return \Mirasvit\Seo\Api\Data\BlogMx\CategoryInterface
     */
    public function getBlogCategory()
    {
        return $this->objectManager->get('\Mirasvit\Seo\Api\Data\BlogMx\CategoryInterface');
    }

    /**
     * @return \Mirasvit\Seo\Api\Config\BlogMxInterface
     */
    public function isEnabled()
    {
        return $this->blogMxConfig->isEnabled();
    }

    /**
     * @param $string
     * @return string
     */
    public function prepareSnippet($string)
    {
        return SnippetsTrait::prepareSnippet($string);
    }
}
