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



namespace Mirasvit\Seo\Block\Adminhtml;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;
use Magento\Framework\Module\Manager ;

class Menu extends AbstractMenu
{
    /**
     * @param Context $context
     */
    public function __construct(
        Context $context,
        Manager $moduleManager
    ) {
        $this->visibleAt(['seo', 'seoautolink']);
        $this->moduleManager = $moduleManager;

        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_Seo::seo_template',
            'title'    => __('Templates'),
            'url'      => $this->urlBuilder->getUrl('seo/template'),
        ])->addItem([
            'resource' => 'Mirasvit_Seo::seo_redirect',
            'title'    => __('Redirects'),
            'url'      => $this->urlBuilder->getUrl('seo/redirect'),
        ])->addItem([
            'resource' => 'Mirasvit_Seo::seo_rewrite',
            'title'    => __('SEO Rewrites'),
            'url'      => $this->urlBuilder->getUrl('seo/rewrite'),
        ])->addItem([
            'resource' => 'Mirasvit_Seo::seo_canonicalRewrite',
            'title'    => __('Canonical Rewrite'),
            'url'      => $this->urlBuilder->getUrl('seo/canonicalRewrite'),
        ]);;

        if ($this->moduleManager->isEnabled('Mirasvit_SeoAutolink')) {
            $this->addItem([
                'resource' => 'Mirasvit_Seo::seoautolink_link',
                'title' => __('Autolinks'),
                'url' => $this->urlBuilder->getUrl('seoautolink/link'),
            ]);
        }

        if ($this->moduleManager->isEnabled('Mirasvit_SeoSitemap')) {
            $this->addItem([
                'resource' => 'Mirasvit_Seo::catalog_sitemap',
                'title' => __('Site Map'),
                'url' => $this->urlBuilder->getUrl('adminhtml/sitemap/'),
            ]);
        }

        $this->addItem([
            'resource' => 'Mirasvit_Seo::seo_settings',
            'title'    => __('Settings'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/seo'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_Seo::seo_manual',
            'title'    => __('User Manual'),
            'url'      => 'http://docs.mirasvit.com/module-seo/current',
        ])->addItem([
            'resource' => 'Mirasvit_Seo::seo_get_support',
            'title'    => __('Get Support'),
            'url'      => 'https://mirasvit.com/support/',
        ]);

        return $this;
    }
}
