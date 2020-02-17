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


namespace Mirasvit\SeoAutolink\Model\Plugin;

use Mirasvit\SeoAutolink\Model\Config;
use Mirasvit\SeoAutolink\Helper\Replace as ReplaceHelper;
use Mirasvit\SeoAutolink\Model\Config\Source\Target;

class CmsPageOutput
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ReplaceHelper
     */
    protected $replaceHelper;

    /**
     * @param Config        $config
     * @param ReplaceHelper $replaceHelper
     */
    public function __construct(
        Config $config,
        ReplaceHelper $replaceHelper
    ) {
        $this->config = $config;
        $this->replaceHelper = $replaceHelper;
    }

    /**
     * @param \Magento\Cms\Model\Page $subject
     * @param \Magento\Cms\Model\Page $result
     * @return \Magento\Cms\Model\Page $result
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPage($subject, $result)
    {
        if ($this->config->isAllowedTarget(Target::CMS_PAGE)) {
            $outputHtml = $this->replaceHelper->addLinks($result->getContent());
            $result->setContent($outputHtml);
        }

        return $result;
    }
}
