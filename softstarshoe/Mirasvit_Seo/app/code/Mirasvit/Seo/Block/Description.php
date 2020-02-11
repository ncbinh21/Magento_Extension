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



namespace Mirasvit\Seo\Block;

/**
 * Блок для вывода SEO описания в футере магазина.
 */
class Description extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Seoautolink\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\SeoAutolink\Helper\Replace
     */
    protected $seoautolinkData;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Mirasvit\Seo\Helper\CurrentSeoData
     */
    protected $currentSeoData;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\SeoAutolink\Model\Config               $config
     * @param \Magento\Framework\ObjectManagerInterface        $objectManager
     * @param \Magento\Framework\Module\Manager                $moduleManager
     * @param \Mirasvit\Seo\Helper\CurrentSeoData              $currentSeoData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\SeoAutolink\Model\Config $config,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Mirasvit\Seo\Helper\CurrentSeoData $currentSeoData,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->config = $config;
        $this->seoautolinkData = $objectManager->get('\Mirasvit\SeoAutolink\Helper\Replace');
        $this->moduleManager = $moduleManager;
        $this->currentSeoData = $currentSeoData;
        $this->context = $context;

        parent::__construct($context, $data);
    }

    /**
     * @param int $position
     * @return bool|string
     */
    public function getDescription($position)
    {
        return $this->currentSeoData->getDescription($position);
    }

    /**
     * @return bool|int
     */
    public function getDescriptionPosition()
    {
        $nameInLayout = $this->getNameInLayout();
        return $this->currentSeoData->getDescriptionPosition(false, $nameInLayout);
    }

}
