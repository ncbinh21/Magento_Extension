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



namespace Mirasvit\Seo\Helper;

use \Mirasvit\Seo\Model\Config as Config;

class CurrentSeoData extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\SeoAutolink\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\SeoAutolink\Helper\Replace
     */
    protected $seoautolinkData;

    /**
     * @var \Mirasvit\Seo\Model\SeoData
     */
    protected $currentSeoData;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @param \Mirasvit\SeoAutolink\Model\Config $config
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Mirasvit\Seo\Model\SeoData $currentSeoData
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        \Mirasvit\SeoAutolink\Model\Config $config,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Mirasvit\Seo\Helper\Data $seoData,
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->config = $config;
        $this->seoautolinkData = $objectManager->get('\Mirasvit\SeoAutolink\Helper\Replace');
        $this->currentSeoData = $seoData;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return \Mirasvit\Seo\Model\SeoData
     */
    public function getCurrentSeoData()
    {
        return $this->currentSeoData->getCurrentSeo();
    }

    /**
     * @param int $position
     * @return bool|string
     */
    public function getDescription($position)
    {
        $currentSeo = $this->getCurrentSeoData();

        if ($position && $currentSeo->getDescriptionPosition() == $position) {
            if ($this->moduleManager->isEnabled('Mirasvit_SeoAutolink')
            && in_array(
                \Mirasvit\SeoAutolink\Model\Config\Source\Target::SEO_DESCRIPTION,
                $this->config->getTarget()
            )) {
                return $this->seoautolinkData->addLinks($currentSeo->getDescription());
            }

            return $currentSeo->getDescription();
        }

        return false;
    }

    /**
     * @param int $position
     * @param bool|string $nameInLayout
     * @return bool|int
     */
    public function getDescriptionPosition($position, $nameInLayout = false)
    {
        if ($position == Config::CUSTOM_TEMPLATE) {
            return $position;
        }

        $position = false;

        if ($nameInLayout == 'm_category_seo_description') {
            $position = Config::UNDER_PRODUCT_LIST;
        } elseif ($nameInLayout == 'seo.description') {
            $position = Config::BOTTOM_PAGE;
        }

        return $position;
    }

    /**
     * @return int
     */
    public function getSeoDescriptionPosition()
    {
        return $this->getCurrentSeoData()->getDescriptionPosition();
    }
}
