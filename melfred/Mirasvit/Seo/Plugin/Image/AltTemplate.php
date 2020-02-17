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



namespace Mirasvit\Seo\Plugin\Image;

class AltTemplate
{
    /**
     * @var array
     */
    protected $parseObjects = [];
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
     */
    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Mirasvit\Seo\Helper\Parse $parse
    ) {
        $this->_config = $config;
        $this->seoParse = $parse;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View\Gallery\ $subject
     * @param \Magento\Framework\Data\Collection $images
     * @return Collection
     */
    public function afterGetGalleryImages($subject, $images)
    {
        if ($this->_config->getIsEnableImageAlt() && $this->_config->getImageAltTemplate()) {
            foreach ($images as $image) {
                $template = $this->_config->getImageAltTemplate();
                $this->parseObjects['product'] = $subject->getProduct();
                $label = $this->seoParse->parse($template, $this->parseObjects);
                $image->setLabel($label);
            }
        }

        return $images;
    }

}