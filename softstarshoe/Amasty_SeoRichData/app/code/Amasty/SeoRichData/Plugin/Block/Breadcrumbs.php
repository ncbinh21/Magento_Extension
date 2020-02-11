<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Plugin\Block;

use Amasty\SeoRichData\Model\DataCollector;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ViewInterface;
use Amasty\SeoRichData\Helper\Config as ConfigHelper;

class Breadcrumbs
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $view;

    /**
     * @var DataCollector
     */
    protected $dataCollector;

    /**
     * @var \Amasty\SeoRichData\Helper\Config
     */
    private $configHelper;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ViewInterface $view,
        DataCollector $dataCollector,
        ConfigHelper $configHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->view = $view;
        $this->dataCollector = $dataCollector;
        $this->configHelper = $configHelper;
    }

    public function beforeAssign(
        \Magento\Theme\Block\Html\Breadcrumbs $subject, $key, $value
    ) {
        if ($key == 'crumbs' && $this->configHelper->isBreadcrumbsEnabled()) {
            $this->dataCollector->setData('breadcrumbs', $value);
        }
    }
}
