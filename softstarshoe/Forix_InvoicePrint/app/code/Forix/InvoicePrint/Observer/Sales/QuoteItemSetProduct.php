<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Start Shoes
 * Date: 27/02/2018
 * Time: 14:47
 */

namespace Forix\InvoicePrint\Observer\Sales;

class QuoteItemSetProduct extends AbstractConvert
{

    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(
        \Magento\Framework\Event\Observer $observer
    ) {
        $product = $observer->getData('product');
        $quoteItem = $observer->getData('quote_item');
        $this->convert($product, $quoteItem, 'sss_product_comment');
    }
}
