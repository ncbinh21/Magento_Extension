<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Controller\Catalog\Adminhtml\Product;

use Aheadworks\Giftcard\Api\Data\AmountInterface;
use Aheadworks\Giftcard\Api\Data\AmountInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\TemplateInterface;
use Aheadworks\Giftcard\Api\Data\TemplateInterfaceFactory;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper as InitializationHelper;
use Magento\Catalog\Model\Product;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Giftcard\Api\Data\ProductAttributeInterface;

/**
 * Class InitializationHelperPlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Controller\Catalog\Adminhtml\Product
 */
class InitializationHelperPlugin
{
    /**
     * @var AmountInterfaceFactory
     */
    private $amountFactory;

    /**
     * @var TemplateInterfaceFactory
     */
    private $templateFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param AmountInterfaceFactory $amountFactory
     * @param TemplateInterfaceFactory $templateFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        AmountInterfaceFactory $amountFactory,
        TemplateInterfaceFactory $templateFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->amountFactory = $amountFactory;
        $this->templateFactory = $templateFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Add Gift card extension attributes after initialize product
     *
     * @param InitializationHelper $subject
     * @param \Closure $proceed
     * @param Product $product
     * @param array $productData
     * @return Product
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundInitializeFromData(
        InitializationHelper $subject,
        \Closure $proceed,
        Product $product,
        array $productData
    ) {
        if ($product->getTypeId() != ProductGiftcard::TYPE_CODE) {
            return $proceed($product, $productData);
        }

        $extension = $product->getExtensionAttributes();
        $extension
            ->setAwGiftcardAmounts($this->getAmounts($productData))
            ->setAwGiftcardTemplates($this->getTemplates($productData));
        $product
            ->setData(ProductAttributeInterface::CODE_AW_GC_AMOUNTS, $extension->getAwGiftcardAmounts())
            ->setData(ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES, $extension->getAwGiftcardTemplates());
        $product->setExtensionAttributes($extension);

        return $proceed($product, $productData);
    }

    /**
     * Retrieve Gift Card amounts
     *
     * @param array $productData
     * @return AmountInterface[]
     */
    private function getAmounts($productData)
    {
        $amounts = [];
        $amountsData = isset($productData[ProductAttributeInterface::CODE_AW_GC_AMOUNTS])
            ? $productData[ProductAttributeInterface::CODE_AW_GC_AMOUNTS]
            : null;

        if (!is_array($amountsData)) {
            return $amounts;
        }
        foreach ($amountsData as $amountData) {
            if (empty($amountData['delete'])) {
                if (isset($amountData['price'])) {
                    $amountData['value'] = $amountData['price'];
                }
                $amountDataObject = $this->amountFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $amountDataObject,
                    $amountData,
                    AmountInterface::class
                );
                $amounts[] = $amountDataObject;
            }
        }
        return $amounts;
    }

    /**
     * Retrieve Gift Card templates
     *
     * @param array $productData
     * @return TemplateInterface[]
     */
    private function getTemplates($productData)
    {
        $templates = [];
        $templatesData = isset($productData[ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES])
            ? $productData[ProductAttributeInterface::CODE_AW_GC_EMAIL_TEMPLATES]
            : null;

        if (!is_array($templatesData)) {
            return $templates;
        }
        foreach ($templatesData as $templateData) {
            if (empty($templateData['delete'])) {
                if (isset($templateData['image'][0])) {
                    $templateData['image'] = $templateData['image'][0]['file'];
                }
                if (isset($templateData['template'])) {
                    $templateData['value'] = $templateData['template'];
                }
                $templateDataObject = $this->templateFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $templateDataObject,
                    $templateData,
                    TemplateInterface::class
                );
                $templates[] = $templateDataObject;
            }
        }
        return $templates;
    }
}
