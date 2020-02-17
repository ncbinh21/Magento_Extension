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



namespace Mirasvit\Seo\Service\Template\SeoDescription;


class Template implements \Mirasvit\Seo\Api\Service\Template\SeoDescription\TemplateInterface
{
    /**
     * @var \Mirasvit\Seo\Helper\CurrentSeoData
     */
    protected $currentSeoData;

    /**
     * @param \Mirasvit\Seo\Helper\CurrentSeoData $currentSeoData
     */
    public function __construct(
        \Mirasvit\Seo\Helper\CurrentSeoData $currentSeoData
    ) {
        $this->currentSeoData = $currentSeoData->getCurrentSeoData();
    }

    /**
     * @return array
     */
    public function getTemplates()
    {
        $template = $this->currentSeoData->getDescriptionTemplate();
        $template = explode("\n", trim($template));
        $template = array_map('trim', $template);
        $template = array_diff($template, [0, null]);

        return $template;
    }
}

