<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product;

use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\CartItemProcessorInterface;
use Magento\Framework\DataObject\Factory as DataObjectFactory;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Giftcard\Api\Data\OptionInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Magento\Quote\Api\Data\ProductOptionInterfaceFactory;
use Magento\Quote\Api\Data\ProductOptionExtensionFactory;

/**
 * Class CartItemProcessor
 *
 * @package Aheadworks\Giftcard\Model\Product
 */
class CartItemProcessor implements CartItemProcessorInterface
{
    /**
     * @var DataObjectFactory
     */
    private $objectFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var OptionInterfaceFactory
     */
    private $giftcardOptionFactory;

    /**
     * @var ProductOptionInterfaceFactory
     */
    private $productOptionFactory;

    /**
     * @var ProductOptionExtensionFactory
     */
    private $extensionFactory;

    /**
     * @param DataObjectFactory $objectFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param OptionInterfaceFactory $giftcardOptionFactory
     * @param ProductOptionInterfaceFactory $productOptionFactory
     * @param ProductOptionExtensionFactory $extensionFactory
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        DataObjectHelper $dataObjectHelper,
        OptionInterfaceFactory $giftcardOptionFactory,
        ProductOptionInterfaceFactory $productOptionFactory,
        ProductOptionExtensionFactory $extensionFactory
    ) {
        $this->objectFactory = $objectFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->giftcardOptionFactory = $giftcardOptionFactory;
        $this->productOptionFactory = $productOptionFactory;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToBuyRequest(CartItemInterface $cartItem)
    {
        $productOptions = $cartItem->getProductOption();
        if ($productOptions && $productOptions->getExtensionAttributes()
            && $productOptions->getExtensionAttributes()->getAwGiftcardOption()
        ) {
            $options = $productOptions->getExtensionAttributes()->getAwGiftcardOption()->getData();
            if (!is_array($options)) {
                return null;
            }
            $requestData = [];
            foreach ($options as $key => $value) {
                $requestData[$key] = $value;
            }
            return $this->objectFactory->create($requestData);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function processOptions(CartItemInterface $cartItem)
    {
        $productOptions = [];
        $options = $cartItem->getOptions();
        if (!is_array($options)) {
            return $cartItem;
        };

        /** @var \Magento\Quote\Model\Quote\Item\Option $option */
        foreach ($options as $option) {
            $productOptions[$option->getCode()] = $option->getValue();
        }
        $giftcardOptionObject = $this->giftcardOptionFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $giftcardOptionObject,
            $productOptions,
            OptionInterface::class
        );

        $productOption = ($cartItem->getProductOption())
            ? $cartItem->getProductOption()
            : $this->productOptionFactory->create();

        $extensibleAttribute =  ($productOption->getExtensionAttributes())
            ? $productOption->getExtensionAttributes()
            : $this->extensionFactory->create();

        $extensibleAttribute->setAwGiftcardOption($giftcardOptionObject);
        $productOption->setExtensionAttributes($extensibleAttribute);
        $cartItem->setProductOption($productOption);

        return $cartItem;
    }
}
