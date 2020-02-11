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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Credit\Observer;

use Mirasvit\Credit\Model\Product\Type;
use Mirasvit\Credit\Ui\DataProvider\Product\Form\Modifier\Composite;
use Magento\Framework\Event\ObserverInterface;

class ProductOptionValueSaveAfter implements ObserverInterface
{
    public function __construct(
        \Mirasvit\Credit\Model\ProductOptionCreditFactory $productOptionCreditFactory,
        \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit $productOptionCreditResource
    ) {
        $this->productOptionCreditFactory  = $productOptionCreditFactory;
        $this->productOptionCreditResource = $productOptionCreditResource;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getEvent()->getObject();

        if (!($product instanceof \Magento\Catalog\Model\Product) || !$product->getId() ||
            $product->getTypeId() != Type::TYPE_CREDITPOINTS) {
            return $this;
        }

        $options    = $this->getCreditOptions($product);
        $optionType = $this->getCreditOptionsType($product);
        /** @var \Mirasvit\Credit\Model\ResourceModel\ProductOptionCredit\Collection $collection */
        $collection = $creditOption = $this->productOptionCreditFactory->create()->getCollection();
        $optionIds  = $collection->addProductFilter($product->getId())
            ->addStoreFilter($product->getStoreId())->getAllIds();
        $optionIds  = array_flip($optionIds);

        $resource = $this->productOptionCreditResource;
        foreach ($options as $option) {
            $creditOptionId = isset($option[Composite::FIELD_OPTION_ID]) ? $option[Composite::FIELD_OPTION_ID] : 0;
            $storeId = isset($option['store_id']) ? $option['store_id'] : $product->getStoreId();
            $creditOption = $this->productOptionCreditFactory->create();
            $resource->load($creditOption, $creditOptionId, $resource->getIdFieldName());
            $creditOption
                ->setOptionProductId($product->getId())
                ->setStoreId($storeId)
                ->setOptionPrice($option[Composite::FIELD_PRICE_NAME])
                ->setOptionPriceOptions($optionType)
            ;
            if ($optionType == Composite::PRICE_TYPE_RANGE) {
                $creditOption->setOptionMinCredits($option[Composite::FIELD_MIN_CREDITS_NAME])
                    ->setOptionMaxCredits($option[Composite::FIELD_MAX_CREDITS_NAME]);
            } else {
                $creditOption->setOptionPriceType($option[Composite::FIELD_PRICE_TYPE_NAME])
                    ->setOptionCredits($option[Composite::FIELD_CREDITS_NAME]);
            }

            $this->productOptionCreditResource->save($creditOption);

            if (isset($optionIds[$creditOptionId])) {
                unset($optionIds[$creditOptionId]);
            }
        }

        foreach ($optionIds as $id => $option) {
            $creditOption = $this->productOptionCreditFactory->create();
            $resource->load($creditOption, $id, $resource->getIdFieldName());
            $this->productOptionCreditResource->delete($creditOption);
        }

        return $this;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    private function getCreditOptionsType($product)
    {
        return $product->getData(Composite::DATA_CREDIT_SCOPE)[Composite::FIELD_TYPE_NAME];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     */
    private function getCreditOptions($product)
    {
        $type    = $this->getCreditOptionsType($product);
        $options = (array)$product->getData(Composite::DATA_CREDIT_SCOPE);
        if ($type == Composite::PRICE_TYPE_FIXED) {
            $options = $options[Composite::CONTAINER_PRICE_GRID_NAME];
        } else {
            $options = [$options];
        }

        return $options;
    }
}