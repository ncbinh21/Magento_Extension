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



namespace Mirasvit\Seo\Block\Adminhtml\System;

use Mirasvit\Seo\Model\Config;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class TemplateRenderer extends AbstractRenderer
{
    /**
     * @param \Magento\Framework\DataObject $template
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function render(\Magento\Framework\DataObject $template)
    {
        $html = '<div class="seo__template-rendered">';

        $values = [
            'meta_title'        => __('Meta title'),
            'meta_keywords'     => __('Meta keywords'),
            'meta_description'  => __('Meta description'),
            'title'             => __('Title (H1)'),
            'description'       => __('SEO description'),
            'short_description' => __('Product short description'),
            'full_description'  => __('Product description'),
        ];

        foreach ($values as $key => $label) {
            $value = trim($template->getData($key)) ? $template->getData($key) : '-';
            $value = $this->highlightTags($value);
            $html .= "<p><b>$label</b>: $value</p>";
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * @param string $string
     * @return string
     */
    public function highlightTags($string)
    {
        $string = preg_replace('/(\[[a-zA-Z_ ,{}|]*\])/', "<span>$1</span>", $string);

        return $string;
    }
}
