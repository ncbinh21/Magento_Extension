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



namespace Mirasvit\Seo\Service\Config;

class BlogMx implements \Mirasvit\Seo\Api\Config\BlogMxInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;


    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->moduleManager = $moduleManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check if Blog Mx enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->moduleManager->isEnabled('Mirasvit_Blog');
    }

    /**
     * Opengraph
     *
     * @return bool
     */
    public function isOgEnabled()
    {
        return $this->isEnabled() && $this->scopeConfig->getValue('seo_snippets/opengraph/is_blogmx_opengraph');
    }

    /**
     * Snippets
     *
     * @return bool
     */
    public function isSnippetsEnabled()
    {
        return $this->isEnabled() && $this->scopeConfig->getValue('seo_snippets/breadcrumbs_snippets/is_breadcrumbs');
    }

    /**
     * Blog Mx actions
     *
     * @return array
     */
    public function getActions()
    {
        return ['blog_post_view', 'blog_category_view', 'blog_category_index'];
    }
}

