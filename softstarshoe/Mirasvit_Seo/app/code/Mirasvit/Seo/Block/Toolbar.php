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

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mirasvit\Seo\Helper\Data as DataHelper;
use Mirasvit\Seo\Helper\Analyzer;
use Mirasvit\Seo\Api\Config\InfoInterface;

/**
 * @method $this setBody($body)
 * @method string getBody()
 */
class Toolbar extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mirasvit_Seo::toolbar.phtml';

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var Analyzer
     */
    protected $analyzer;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param InfoInterface $info
     * @param DataHelper $dataHelper
     * @param Analyzer   $analyzer
     * @param Context    $context
     */
    public function __construct(
        InfoInterface $info,
        DataHelper $dataHelper,
        Analyzer $analyzer,
        Context $context
    ) {
        $this->info = $info;
        $this->dataHelper = $dataHelper;
        $this->analyzer = $analyzer;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function getSections()
    {
        $pageConfig = $this->context->getPageConfig();

        return [
            [
                'label' => __('Full Action Name'),
                'data'  => [
                    'value'  => $this->dataHelper->getFullActionCode(),
                    'status' => ''
                ]
            ],
            [
                'label' => __('Robots Meta Header'),
                'data'  => [
                    'value'  => $pageConfig->getRobots(),
                    'status' => '',
                ]
            ],
            [
                'label' => __('Canonical URL'),
                'data'  => $this->analyzer->getCanonicalStatus($this->getBody()),
            ],
            [
                'label' => __('H1 Header'),
                'data'  => $this->analyzer->getHeaderStatus($this->getBody()),
            ],
            [
                'label' => __('Meta Title'),
                'data'  => $this->analyzer->getMetaTitleStatus($this->getBody()),
            ],
            [
                'label' => __('Meta Description'),
                'data'  => $this->analyzer->getMetaDescriptionStatus($this->getBody()),
            ],
            [
                'label' => __('Meta Keywords'),
                'data'  => $this->analyzer->getMetaKeywordsStatus($this->getBody()),
            ],
            [
                'label' => __('Image Alt'),
                'data'  => $this->analyzer->getImagesStatus($this->getBody()),
            ]
        ];
    }

    /**
     * @return array
     */
    public function getPosition()
    {
        $position = $this->info->getPosition();
        $positionStyle = 'right: 1rem !important;';
        if ($position == InfoInterface::INFO_POSITION_LEFT) {
            $positionStyle = "left: 1rem !important; right: auto;";
        }
        return $positionStyle;
    }
}