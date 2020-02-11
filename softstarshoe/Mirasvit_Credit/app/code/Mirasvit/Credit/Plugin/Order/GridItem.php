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


namespace Mirasvit\Credit\Plugin\Order;

use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid;

class GridItem
{
    public function __construct(
        \Mirasvit\Credit\Helper\CreditOption $optionHelper,
        \Mirasvit\Credit\Model\ProductOptionCreditFactory $productOptionCredit
    ) {
        $this->optionHelper        = $optionHelper;
        $this->productOptionCredit = $productOptionCredit;
    }

    /**
     * @param Grid      $subject
     * @param \callable $proceed
     * @param Item      $item
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetItemUnitPriceHtml(Grid $subject, $proceed, Item $item)
    {
        $result = $proceed($item);

        $result .= $this->getCreditHtml($item);

        return $result;
    }

    /**
     * @param Grid      $subject
     * @param \callable $proceed
     * @param Item      $item
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetItemRowTotalHtml(Grid $subject, $proceed, Item $item)
    {
        $result = $proceed($item);

        $result .= $this->getCreditHtml($item, $item->getQty());

        return $result;
    }

    /**
     * @param Item $item
     * @param int  $qty
     * @return string
     */
    protected function getCreditHtml(Item $item, $qty = 1)
    {
        $html = '';
        if ($item->getProductType() == \Mirasvit\Credit\Model\Product\Type::TYPE_CREDITPOINTS) {
            $option = $this->productOptionCredit->create();
            $value  = $item->getProduct()->getCustomOption('option_creditOption');
            $data   = (array)$item->getBuyRequest()->getData('creditOptionData');
            $option->setData($data);

            $credits = $this->optionHelper->getOptionCredits($option, $value) * $qty;

            $html .= '<br>';
            $html .= __('Store Credits: %1', $credits);
            $html .= '<br>';
        }

        return $html;
    }
}