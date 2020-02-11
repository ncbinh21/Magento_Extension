<?php

namespace Forix\Checkout\Plugin\Cart\Totals;

use Magento\Quote\Model\Cart\Totals\ItemConverter;
use Magento\Quote\Api\Data\TotalsItemExtensionFactory;

class ItemConverterPlugin
{
    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $itemFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productloader;

    /**
     * @var TotalsItemExtensionFactory
     */
    protected $totalsItemExtensionFactory;

    /**
     * ItemConverterPlugin constructor.
     *
     * @param TotalsItemExtensionFactory $totalsItemExtensionFactory
     */
    public function __construct(
        TotalsItemExtensionFactory $totalsItemExtensionFactory,
        \Magento\Catalog\Model\ProductFactory $productloader,
        \Magento\Quote\Model\Quote\ItemFactory $itemFactory
    ) {
        $this->totalsItemExtensionFactory = $totalsItemExtensionFactory;
        $this->productloader = $productloader;
        $this->itemFactory = $itemFactory;
    }

    /**
     * @param ItemConverter $subject
     *
     * @param $result
     * @return mixed
     */
    public function afterModelToDataObject(
        ItemConverter $subject,
        $result
    ) {
        $extensionAttributes = $result->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->totalsItemExtensionFactory->create();
        }
        $item = $this->itemFactory->create()->load($result->getItemId());
        $product = $this->productloader->create()->load($item->getProductId());
        $extensionAttributes->setProductRestriction('0');
        if($product->getSssRestriction()){
            $extensionAttributes->setProductRestriction('1');
        }
        $result->setExtensionAttributes($extensionAttributes);
        return $result;
    }

    /**
     * @param ItemConverter $subject
     * @param $item
     * @return mixed
     */
    public function beforeModelToDataObject(
        ItemConverter $subject,
        $result
    ) {
        if($options = $result->getProduct()->getOptions()) {
            foreach ($options as $key => $value) {
                if ($value->getType() == 'checkbox') {
                    unset($options[$key]);
                }
            }
            $result->getProduct()->setOptions($options);
        }
    }
}