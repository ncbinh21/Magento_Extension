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



namespace Mirasvit\Seo\Plugin\Template;

use Magento\Framework\View\TemplateEngineFactory;
use Magento\Framework\View\TemplateEngineInterface;
use Mirasvit\Seo\Api\Service\Template\SeoDescription\TemplateInterface;
use Mirasvit\Seo\Service\Template\SeoDescription\AddInTemplateFactory;
use \Mirasvit\Seo\Model\Config as Config;
use \Mirasvit\Seo\Helper\CurrentSeoData;


class AddSeoDescriptionUnderTemplate
{
    /**
     * @var AddInTemplateFactory
     */
    protected $addInTemplate;

    /**
     * @var TemplateInterface
     */
    protected $template;

    /**
     * @var CurrentSeoData
     */
    protected $currentSeoData;

    /**
     * @param AddInTemplateFactory $addInTemplate
     * @param TemplateInterface $template
     * @param StoreManagerInterface $storeManager
     * @param CurrentSeoData $currentSeoData
     */
    public function __construct(
        AddInTemplateFactory $addInTemplate,
        TemplateInterface $template,
        CurrentSeoData $currentSeoData
    ) {
        $this->addInTemplate = $addInTemplate;
        $this->template = $template;
        $this->currentSeoData = $currentSeoData;
    }

    /**
     * Add SEO Description in templates depending of SEO Templates configuration
     *
     * @param TemplateEngineFactory $subject
     * @param TemplateEngineInterface $invocationResult
     *
     * @return TemplateEngineInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(
        TemplateEngineFactory $subject,
        TemplateEngineInterface $invocationResult
    ) {
        $templates = $this->template->getTemplates();
        $descriptionPosition = $this->currentSeoData->getSeoDescriptionPosition();
        if ($templates && $descriptionPosition == Config::CUSTOM_TEMPLATE) {
            $seoDescription = $this->currentSeoData->getDescription($descriptionPosition);
            return $this->addInTemplate->create([
                'subject' => $invocationResult,
                'templates' => $templates,
                'seoDescription' => $seoDescription,
            ]);
        }

        return $invocationResult;
    }
}
