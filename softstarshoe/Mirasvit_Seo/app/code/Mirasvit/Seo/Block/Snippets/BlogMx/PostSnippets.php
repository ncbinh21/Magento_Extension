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



namespace Mirasvit\Seo\Block\Snippets\BlogMx;

class PostSnippets extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Seo\Api\Config\BlogMxInterface
     */
    protected $blogMxConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param  \Mirasvit\Seo\Api\Data\BlogMx\PostInterface $post
     * @param \Magento\Framework\View\Element\Template\Contex $context
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
     * @return \Mirasvit\Seo\Api\Data\BlogMx\PostInterface
     */
    public function getPost()
    {
        return $this->objectManager->get('\Mirasvit\Seo\Api\Data\BlogMx\PostInterface');
    }

    /**
     * @return \Mirasvit\Seo\Api\Config\BlogMxInterface
     */
    public function isEnabled()
    {
        return $this->blogMxConfig->isEnabled();
    }
}
