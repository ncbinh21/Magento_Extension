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



namespace Mirasvit\Seo\Helper\Rewrite;

class Image extends \Magento\Catalog\Helper\Image
{

    /**
     * @var array
     */
    protected $parseObjects = array();

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $_config;

    /**
     * @var \Mirasvit\Seo\Helper\Parse
     */
    protected $seoParse;

    /**
     * @param \Mirasvit\Seo\Model\Config $config
     * @param \Mirasvit\Seo\Helper\Parse $parse
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Catalog\Model\Product\ImageFactory $productImageFactory
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\View\ConfigInterface $viewConfig
     */
    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Mirasvit\Seo\Helper\Parse $parse,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Model\Product\ImageFactory $productImageFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\View\ConfigInterface $viewConfig
    ) {
        $this->_config = $config;
        $this->seoParse = $parse;
        parent::__construct($context, $productImageFactory, $assetRepo, $viewConfig);
    }

    /**
     * Return image label
     *
     * @return string
     */
    public function getLabel()
    {
        if ($this->_config->getIsEnableImageAlt() && $this->_config->getImageAltTemplate()) {
            $template = $this->_config->getImageAltTemplate();
            $this->parseObjects['product'] = $this->_product;
            $label = $this->seoParse->parse($template, $this->parseObjects);
        } else {
            $label = $this->_product->getData($this->getType() . '_' . 'label');
            if (empty($label)) {
                $label = $this->_product->getName();
            }
        }

        return $label;
    }
}