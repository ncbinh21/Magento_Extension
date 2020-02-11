<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 16
 * Time: 14:51
 */
namespace Forix\InvoicePrint\Plugin;

class QuoteItemProductOption
{

    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);
        $attributeCodes = ['sss_product_comment'];
        foreach ($attributeCodes as $attributeCode) {
            if ($item->getData($attributeCode)) {
                $orderItem->setData($attributeCode, $item->getData($attributeCode));
            }
        }

        return $orderItem;
    }
}