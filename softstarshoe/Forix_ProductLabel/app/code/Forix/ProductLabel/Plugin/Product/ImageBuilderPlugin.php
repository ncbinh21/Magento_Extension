<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Plugin\Product;

use Forix\ProductLabel\Block\Product\ImageBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Forix\ProductLabel\Helper\Data as HelperData;

/**
 * Class ImageBuilder
 * @package Forix\ProductLabel\Plugin\Product
 */
class ImageBuilderPlugin
{
    /**@#%
     * @const Xml path
     */
    const XML_PATH_IS_ENABLE_PRODUCT_LABEL = 'productlabel/settings/active';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * ImageBuilderPlugin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param HelperData $helperData
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        HelperData $helperData
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->helperData = $helperData;
    }

    /**
     * @param ImageBuilder $subject
     * @param \Closure $proceed
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function aroundCreate(ImageBuilder $subject, \Closure $proceed)
    {
        if (!$this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLE_PRODUCT_LABEL)) {
            return $proceed();
        }
        try {
            $data = $subject->prepareData();
            $data['data']['product_label'] = $this->helperData->getLabels($subject->getProduct());

            return $subject->getImageBlock()->create($data);
        } catch (\Exception $e) {
            return $proceed();
        }
    }
}
