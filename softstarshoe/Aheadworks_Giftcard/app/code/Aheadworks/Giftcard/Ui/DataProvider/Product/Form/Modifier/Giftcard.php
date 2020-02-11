<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Ui\DataProvider\Product\Form\Modifier;

use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Ui\DataProvider\Product\Form\Modifier
 */
class Giftcard extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $poolFieldPath = $this->arrayManager
            ->findPath(ProductAttributeInterface::CODE_AW_GC_POOL, $meta, null, 'children');

        if ($poolFieldPath) {
            $usedDefault = $this->locator->getProduct()->getData(ProductAttributeInterface::CODE_AW_GC_POOL) === null;

            $meta = $this->arrayManager->merge(
                $poolFieldPath,
                $meta,
                [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'service' => [
                                    'template' =>
                                        'Aheadworks_Giftcard/ui/form/element/giftcard/helper/service-settings',
                                    'configSettingsUrl' =>
                                        $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/aw_giftcard'),
                                    'label' => __('Use pattern from')
                                ],
                                'usedDefault' => $usedDefault,
                                'disabled' => $usedDefault,
                                'validation' => [
                                    'validate-select' => true
                                ]
                            ],
                        ],
                    ]
                ]
            );
        }

        return $meta;
    }
}
