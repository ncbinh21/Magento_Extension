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


namespace Mirasvit\Credit\Block\Adminhtml\Transaction;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Mirasvit\Credit\Model\ResourceModel\Balance\CollectionFactory as BalanceCollectionFactory;
use Mirasvit\Credit\Model\ResourceModel\Transaction\CollectionFactory as TransactionCollectionFactory;
use Magento\Framework\Locale\CurrencyInterface;

class QuickReport extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mirasvit_Credit::transaction/quick_report.phtml';
    /**
     * @var BalanceCollectionFactory
     */
    private $balanceCollectionFactory;

    /**
     * @var TransactionCollectionFactory
     */
    private $transactionCollectionFactory;

    /**
     * @var CurrencyInterface
     */
    private $currency;

    /**
     * @var Context
     */
    private $context;

    public function __construct(
        BalanceCollectionFactory $balanceCollectionFactory,
        TransactionCollectionFactory $transactionCollectionFactory,
        CurrencyInterface $currency,
        Context $context
    ) {
        $this->balanceCollectionFactory = $balanceCollectionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->currency = $currency;
        $this->context = $context;

        parent::__construct($context);
    }

    /**
     * @return float
     */
    public function getTotalBalance()
    {
        return $this->balanceCollectionFactory->create()->getTotalBalance();
    }

    /**
     * @return float
     */
    public function getTotalUsedBalance()
    {
        return $this->transactionCollectionFactory->create()->getTotalUsedAmount();
    }

    /**
     * @param number $amount
     * @return string
     */
    public function getCurrency($amount)
    {
        return $this->currency->getCurrency($this->context->getStoreManager()->getStore()->getBaseCurrencyCode())
            ->toCurrency($amount);
    }
}