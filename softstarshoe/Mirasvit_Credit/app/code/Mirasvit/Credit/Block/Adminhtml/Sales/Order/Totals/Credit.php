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



namespace Mirasvit\Credit\Block\Adminhtml\Sales\Order\Totals;

/**
 * Totals item block.
 */
class Credit extends \Magento\Sales\Block\Adminhtml\Order\Totals
{

    public function __construct(
        \Mirasvit\Credit\Helper\CreditOption $optionHelper,
        \Mirasvit\Credit\Model\ProductOptionCreditFactory $productOptionCreditFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper, $data);

        $this->optionHelper               = $optionHelper;
        $this->productOptionCreditFactory = $productOptionCreditFactory;
    }
    /**
     * Determine display parameters before rendering HTML.
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();

        $this->setCanDisplayTotalPaid($this->getParentBlock()->getCanDisplayTotalPaid());
        $this->setCanDisplayTotalRefunded($this->getParentBlock()->getCanDisplayTotalRefunded());
        $this->setCanDisplayTotalDue($this->getParentBlock()->getCanDisplayTotalDue());

        return $this;
    }

    /**
     * Initialize totals object.
     *
     * @return $this
     */
    public function initTotals()
    {
        $total = new \Magento\Framework\DataObject([
            'code'       => $this->getNameInLayout(),
            'block_name' => $this->getNameInLayout(),
            'area'       => $this->getDisplayArea(),
            'strong'     => $this->getStrong(),
        ]);

        $this->getParentBlock()->addTotal($total, $this->getAfterCondition());

        return $this;
    }

    /**
     * Retrieve current order model instance.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Price HTML getter.
     *
     * @param float $baseAmount
     * @param float $amount
     *
     * @return string
     */
    public function displayPrices($baseAmount, $amount)
    {
        return $this->_adminHelper->displayPrices($this->getOrder(), $baseAmount, $amount);
    }

    /**
     * Price attribute HTML getter.
     *
     * @param string $code
     * @param bool   $strong
     * @param string $separator
     *
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->_adminHelper->displayPriceAttribute($this->getSource(), $code, $strong, $separator);
    }

    /**
     * Source order getter.
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return int
     */
    public function getPurchasedCredits()
    {
        $order = $this->getSource();

        $credits = 0;
        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllItems() as $item) {
            if ($item->getProductType() == \Mirasvit\Credit\Model\Product\Type::TYPE_CREDITPOINTS
                || $item->getRealProductType() == \Mirasvit\Credit\Model\Product\Type::TYPE_CREDITPOINTS
            ) {
                $productOptions = $item->getProductOptions();
                if (!isset($productOptions['info_buyRequest'])) {
                    continue;
                }
                $requestOptions = $productOptions['info_buyRequest'];

                $option = $this->productOptionCreditFactory->create();
                $value  = !empty($requestOptions['creditOption']) ? $requestOptions['creditOption'] : 0;
                $data   = !empty($requestOptions['creditOptionData']) ? $requestOptions['creditOptionData'] : [];
                $option->setData($data);

                $credits += $this->optionHelper->getOptionCredits($option, $value) * $item->getQtyInvoiced();
            }
        }

        return $credits;
    }
}
