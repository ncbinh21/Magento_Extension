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



namespace Mirasvit\Seo\Service\Template\SeoDescription;

class AddInTemplate implements \Mirasvit\Seo\Api\Service\Template\SeoDescription\AddInTemplateInterface,
    \Magento\Framework\View\TemplateEngineInterface
{
    /**
     * @var \Magento\Framework\View\TemplateEngineInterface
     */
    private $_subject;

    /**
     * @var array
     */
    private $_templates;

    /**
     * @var string
     */
    private $_seoDescription;


    /**
     * @param \Magento\Framework\View\TemplateEngineInterface $subject
     * @param array $templates
     * @param \Mirasvit\SeoAutolink\Helper\Replace $replaceHelper
     */
    public function __construct(\Magento\Framework\View\TemplateEngineInterface $subject, $templates, $seoDescription)
    {
        $this->_subject = $subject;
        $this->_templates = $templates;
        $this->_seoDescription = $seoDescription;
    }

    /**
     * Insert autolinks into the rendered block contents
     *
     * {@inheritdoc}
     */
    public function render(\Magento\Framework\View\Element\BlockInterface $block, $templateFile, array $dictionary = [])
    {
        $result = $this->_subject->render($block, $templateFile, $dictionary);

        $isTemplateUsed = array_filter($this->_templates,
            function ($el) use ($templateFile) {
                return strpos($templateFile, $el) !== false;
            });

        if ($isTemplateUsed) {
            $result = $this->addSeoDescription($result);
        }

        return $result;
    }

    /**
     * @param string $result
     * @return string
     */
    public function addSeoDescription($result)
    {
        if ($this->_seoDescription) {
            $result = $result . $this->_seoDescription;
        }

        return $result;
    }
}

