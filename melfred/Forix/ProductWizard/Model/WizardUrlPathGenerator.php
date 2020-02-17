<?php

namespace Forix\ProductWizard\Model;

use Forix\ProductWizard\Api\Data\WizardInterface;

/**
 * @api
 * @since 100.0.2
 */
class WizardUrlPathGenerator
{

    /**
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filterManager;

    /**
     */
    public function __construct(
        \Magento\Framework\Filter\FilterManager $filterManager
    )
    {
        $this->filterManager = $filterManager;
    }

    /**
     * @param WizardInterface $wizard
     *
     * @return string
     */
    public function getUrlPath(WizardInterface $wizard)
    {
        return $wizard->getIdentifier();
    }

    /**
     * Get canonical product url path
     *
     * @param WizardInterface $wizard
     * @return string
     */
    public function getCanonicalUrlPath(WizardInterface $wizard)
    {
        return 'product-wizard/index/index/wizard_id/' . $wizard->getId();
    }

    /**
     * Generate CMS page url key based on url_key entered by merchant or page title
     *
     * @param WizardInterface $wizard
     * @return string
     */
    public function generateUrlKey(WizardInterface $wizard)
    {
        $urlKey = $wizard->getIdentifier();
        return $this->filterManager->translitUrl($urlKey === '' || $urlKey === null ? $wizard->getTitle() : $urlKey);
    }
}
