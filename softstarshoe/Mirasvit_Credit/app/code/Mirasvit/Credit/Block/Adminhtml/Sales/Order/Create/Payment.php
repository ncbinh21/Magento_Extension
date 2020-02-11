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



namespace Mirasvit\Credit\Block\Adminhtml\Sales\Order\Create;

use Mirasvit\Credit\Model\Config;

class Payment extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Magento\Sales\Model\AdminOrder\Create
     */
    protected $salesOrderCreate;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Credit\Model\BalanceFactory  $balanceFactory
     * @param \Magento\Sales\Model\AdminOrder\Create $salesOrderCreate
     * @param \Magento\Backend\Model\Session\Quote   $sessionQuote
     * @param \Magento\Backend\Block\Widget\Context  $context
     * @param array                                  $data
     */
    public function __construct(
        \Mirasvit\Credit\Model\BalanceFactory $balanceFactory,
        \Magento\Sales\Model\AdminOrder\Create $salesOrderCreate,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->salesOrderCreate = $salesOrderCreate;
        $this->sessionQuote = $sessionQuote;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @var \Mirasvit\Credit\Model\Balance
     */
    protected $balance;

    /**
     * @return \Magento\Sales\Model\AdminOrder\Create
     */
    protected function _getOrderCreateModel()
    {
        return $this->salesOrderCreate;
    }

    /**
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->sessionQuote->getOrder()->formatPrice($value);
    }

    /**
     * @return bool|float
     */
    public function getBalance()
    {
        if (!$this->_getBalance()) {
            return false;
        }

        return $this->_getBalance()->getAmount();
    }

    /**
     * @return bool|int
     */
    public function getUseCredit()
    {
        return $this->_getOrderCreateModel()->getQuote()->getUseCredit() == Config::USE_CREDIT_YES;
    }

    /**
     * @return bool
     */
    public function isFullyPaid()
    {
        if (!$this->_getBalance()) {
            return false;
        }

        return $this->_getBalance()->isFullAmountCovered($this->_getOrderCreateModel()->getQuote());
    }

    /**
     * @return bool
     */
    public function canUseCredit()
    {
        return $this->getBalance() > 0;
    }

    /**
     * @return bool|\Mirasvit\Credit\Model\Balance
     */
    protected function _getBalance()
    {
        if (!$this->balance) {
            $quote = $this->_getOrderCreateModel()->getQuote();

            if (!$quote || !$quote->getCustomerId()) {
                return false;
            }

            $this->balance = $this->balanceFactory->create()->loadByCustomer($quote->getCustomerId());
        }

        return $this->balance;
    }

    /**
     * @return bool
     */
    public function canUseCustomerBalance()
    {
        $quote = $this->_getOrderCreateModel()->getQuote();

        return $this->getBalance() && ($quote->getBaseGrandTotal() + $quote->getBaseCustomerBalanceAmountUsed() > 0);
    }
}
