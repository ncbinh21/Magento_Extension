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



namespace Mirasvit\SeoAutolink\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function getTarget()
    {
        return explode(',', $this->scopeConfig->getValue('seoautolink/autolink/target'));
    }

    /**
     * @param string $target
     * @return bool
     */
    public function isAllowedTarget($target)
    {
        return in_array($target, $this->getTarget());
    }

    /**
     * @param int $store
     * @return array
     */
    public function getExcludedTags($store = null)
    {
        $conf = $this->scopeConfig->getValue(
            'seoautolink/autolink/excluded_tags',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );

        $tags = explode("\n", trim($conf));
        $tags = array_map('trim', $tags);
        $tags = array_diff($tags, [0, null]);

        return $tags;
    }

    /**
     * @param int $store
     * @return array
     */
    public function getSkipLinks($store = null)
    {
        $conf = $this->scopeConfig->getValue(
            'seoautolink/autolink/skip_links_for_page',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );

        $links = explode("\n", trim($conf));
        $links = array_map('trim', $links);
        $links = array_diff($links, [0, null]);

        return $links;
    }

    /**
     * @param int $store
     * @return int
     */
    public function getLinksLimitPerPage($store = null)
    {
        $linksLimit = $this->scopeConfig->getValue(
            'seoautolink/autolink/links_limit_per_page',
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );

        if (empty($linksLimit) || (int)$linksLimit == 0) {
            return false;
        }

        return $linksLimit;
    }
}
