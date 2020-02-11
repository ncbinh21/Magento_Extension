<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Product\Option;

use Aheadworks\Giftcard\Api\Data\OptionInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Magento\Catalog\Api\Data\ProductOptionInterface;
use Magento\Catalog\Model\ProductOptionProcessorInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory as DataObjectFactory;

/**
 * Class Processor
 *
 * @package Aheadworks\Giftcard\Model\Product\Option
 */
class Processor implements ProductOptionProcessorInterface
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
    private $optionFactory;

    /**
     * @param DataObjectFactory $objectFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param OptionInterfaceFactory $optionFactory
     */
    public function __construct(
        DataObjectFactory $objectFactory,
        DataObjectHelper $dataObjectHelper,
        OptionInterfaceFactory $optionFactory
    ) {
        $this->objectFactory = $objectFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->optionFactory = $optionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToBuyRequest(ProductOptionInterface $productOption)
    {
        /** @var DataObject $request */
        $request = $this->objectFactory->create();

        $giftcardOptions = $this->getGiftcardOptions($productOption);
        if (!empty($giftcardOptions)) {
            $request->addData($giftcardOptions);
        }
        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToProductOption(DataObject $request)
    {
        $options = [];
        $requestOptions = $request->getData();
        foreach ($requestOptions as $optionKey => $optionValue) {
            $options[$optionKey] = $optionValue;
        }

        if (!empty($options) && is_array($options)) {
            $giftcardOptionObject = $this->optionFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $giftcardOptionObject,
                $options,
                OptionInterface::class
            );
            return ['aw_giftcard_option' => $giftcardOptionObject];
        };

        return [];
    }

    /**
     * Retrieve Gift Card options
     *
     * @param ProductOptionInterface $productOption
     * @return array
     */
    private function getGiftcardOptions(ProductOptionInterface $productOption)
    {
        if ($productOption
            && $productOption->getExtensionAttributes()
            && $productOption->getExtensionAttributes()->getGiftcardItemOption()
        ) {
            return $productOption->getExtensionAttributes()->getAwGiftcardOption()->getData();
        }
        return [];
    }
}
