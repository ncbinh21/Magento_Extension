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


namespace Mirasvit\Credit\Block\Adminhtml\Sales\Order\View\Items;

class Renderer extends \Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer
{
    public function __construct(
        \Mirasvit\Credit\Helper\CreditOption $optionHelper,
        \Mirasvit\Credit\Model\ProductOptionCreditFactory $productOptionCredit,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        array $data = []
    ) {
        parent::__construct(
            $context, $stockRegistry, $stockConfiguration, $registry, $messageHelper, $checkoutHelper, $data
        );

        $this->optionHelper        = $optionHelper;
        $this->productOptionCredit = $productOptionCredit;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @param string                        $column
     * @param null                          $field
     * @return string
     */
    public function getColumnHtml(\Magento\Framework\DataObject $item, $column, $field = null)
    {
        $html = parent::getColumnHtml($item, $column, $field);

        if ($column == 'credit') {
            $html .= $this->getCreditHtml($item);
        }

        return $html;
    }

    /**
     * @param \Magento\Sales\Model\Order\Invoice\Item $item
     * @return string
     */
    protected function getCreditHtml($item)
    {
        $credits = 0;
        if ($item->getProductType() == \Mirasvit\Credit\Model\Product\Type::TYPE_CREDITPOINTS) {
            $option = $this->productOptionCredit->create();
            $value  = $item->getBuyRequest()->getData('creditOption', 0);
            $data   = (array)$item->getBuyRequest()->getData('creditOptionData');
            $option->setData($data);
            $credits = $this->optionHelper->getOptionCredits($option, $value) * $item->getQtyOrdered();
        }

        return $credits;
    }
}
