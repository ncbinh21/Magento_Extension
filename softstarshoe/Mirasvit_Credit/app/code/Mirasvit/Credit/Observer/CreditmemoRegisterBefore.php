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

use Magento\Framework\Event\ObserverInterface;

class CreditmemoRegisterBefore implements ObserverInterface
{
    /**
     * @var \Mirasvit\Credit\Model\BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var \Mirasvit\Credit\Helper\Data
     */
    protected $creditData;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @param \Mirasvit\Credit\Model\BalanceFactory $balanceFactory
     * @param \Mirasvit\Credit\Helper\Data          $creditData
     * @param \Magento\Framework\App\Request\Http   $request
     */
    public function __construct(
        \Mirasvit\Credit\Model\BalanceFactory $balanceFactory,
        \Mirasvit\Credit\Helper\Data $creditData,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->creditData = $creditData;
        $this->request = $request;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order\Creditmemo $creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();

        $input = $this->request->getParam('creditmemo');

        $amount = 0;
        if (isset($input['refund_to_credit_amount'])) {
            $amount = floatval($input['refund_to_credit_amount']);
        }

        if ($creditmemo->getBaseCreditAmount() + $creditmemo->getBaseCreditReturnMax() > 0) {
            if ($creditmemo->getBaseCreditReturnMax() <= 0) {
                $amount = $creditmemo->getBaseCreditAmount();
            } else {
                $amount = $creditmemo->getBaseCreditAmount() + min($creditmemo->getBaseCreditReturnMax(), $amount);
            }
        }

        if ($amount > 0) {
            $amount = $creditmemo->roundPrice($amount);

            $creditmemo->setBaseCreditTotalRefunded($amount);
            $creditmemo->setCreditTotalRefunded($amount);

            $creditmemo->setCreditRefundFlag(true);
            $creditmemo->setPaymentRefundDisallowed(false);
        }
    }
}
