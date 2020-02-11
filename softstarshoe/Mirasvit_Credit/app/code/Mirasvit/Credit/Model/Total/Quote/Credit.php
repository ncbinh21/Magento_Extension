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



namespace Mirasvit\Credit\Model\Total\Quote;

use Mirasvit\Credit\Model\Config;
use Mirasvit\Credit\Model\BalanceFactory;
use Mirasvit\Credit\Helper\Calculation;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\Quote\Address\Total as AddressTotal;

class Credit extends AbstractTotal
{
    /**
     * @var array
     */
    private $processedShippingAddresses = [];

    /**
     * @var int
     */
    private $amountUsed = 0;

    /**
     * @var int
     */
    private $baseAmountUsed = 0;

    /**
     * @var Calculation
     */
    private $calculationHelper;

    /**
     * @var PricingHelper
     */
    private $currencyHelper;

    /**
     * @var BalanceFactory
     */
    private $balanceFactory;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Calculation $calculationHelper,
        PricingHelper $currencyHelper,
        BalanceFactory $balanceFactory,
        Config $config
    ) {
        $this->calculationHelper = $calculationHelper;
        $this->currencyHelper    = $currencyHelper;
        $this->balanceFactory    = $balanceFactory;
        $this->config            = $config;
    }

    /**
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param AddressTotal                $total
     *
     * @return $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        AddressTotal $total
    ) {
        if ($quote->getUseCredit() == Config::USE_CREDIT_NO) {
            return $this;
        }


        if ($quote->getUseCredit() == Config::USE_CREDIT_UNDEFINED && !$this->config->isAutoApplyEnabled()) {
            return $this;
        }

        if (!$quote->getCustomer() || !$quote->getCustomer()->getId()) {
            return $this;
        }

        $quote->setUseCredit(Config::USE_CREDIT_YES);

        $this->fixQuoteRegion($quote);

        $quote->setBaseCreditAmountUsed(0)
            ->setCreditAmountUsed(0)
            ->save();

        $address = $shippingAssignment->getShipping()->getAddress();

        $this->resetMultishippingTotalsOnRecollection($quote, $address->getId());

        $baseUsed = $this->balanceFactory->create()
            ->loadByCustomer($quote->getCustomerId())
            ->getAmount();
        $used = $this->currencyHelper->currency($baseUsed, false, false);

        $customerUsed = $used;
        $customerBaseUsed = $baseUsed;
        if ($quote->getIsMultiShipping()) {
            $used -= $this->amountUsed;
            $baseUsed -= $this->baseAmountUsed;
        } else {
            $this->amountUsed = $used;
            $this->baseAmountUsed = $baseUsed;
        }

        if ($used > $total->getGrandTotal() && /*(float)$total->getGrandTotal() &&*/
            $total->getGrandTotal() >= 0
        ) {
            $used     = $total->getGrandTotal();
            $baseUsed = $total->getBaseGrandTotal();
        }
        $maxUsed     = $this->calculationHelper->calc(
            $total->getGrandTotal(), $total->getTotalAmount('tax'), $total->getTotalAmount('shipping')
        );
        if ($maxUsed < $used) {
            $used = $maxUsed;
        }
        if ($maxUsed < $baseUsed) {
            $baseUsed = $maxUsed;
        }

        if ($quote->getIsMultiShipping()) {
            $this->amountUsed += $used;
            $this->baseAmountUsed += $baseUsed;

            if ($this->amountUsed > $customerUsed) {
                $this->amountUsed = $customerUsed;
                $this->baseAmountUsed = $customerBaseUsed;
            }

            $this->processedShippingAddresses[$address->getId()] = $used;
        }

        $quote->setBaseCreditAmountUsed($this->baseAmountUsed)
            ->setCreditAmountUsed($this->amountUsed);

        $address->setBaseCreditAmount($baseUsed)
            ->setCreditAmount($used)
            ->save();

        $total->setBaseTotalAmount($this->getCode(), $baseUsed);
        $total->setTotalAmount($this->getCode(), $used);

        $total->setBaseGrandTotal($total->getBaseGrandTotal() - $baseUsed);
        $total->setGrandTotal($total->getGrandTotal() - $used);

        $total->setBaseCreditAmount($baseUsed);
        $total->setCreditAmount($used);

        $quote->setCreditCollected(true)
            ->save();

        return $this;
    }

    /**
     * @param Quote        $quote
     * @param AddressTotal $total
     * @return array|null
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, AddressTotal $total)
    {
        $creditTotal = null;
        if ($quote->getIsVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        if ($quote->getUseCredit() == Config::USE_CREDIT_YES && (float)$total->getCreditAmount()) {
            $creditTotal = [
                'code'  => $this->getCode(),
                'title' => __('Store Credit'),
                'value' => -$total->getCreditAmount(),
            ];

            $address->addTotal($creditTotal);
        }

        return $creditTotal;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param int                        $addressId
     *
     * @return void
     */
    protected function resetMultishippingTotalsOnRecollection($quote, $addressId)
    {
        if (
            $quote->getIsMultiShipping()
            && !empty($this->processedShippingAddresses[$addressId])
            && $this->amountUsed
        ) {
            $this->amountUsed = 0;
            $this->processedShippingAddresses = [];
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return void
     */
    protected function fixQuoteRegion($quote)
    {
        /** @var \Magento\Customer\Model\Data\Region $region */
        /** @var \Magento\Framework\App\PageCache\Version $region */
        $region = $quote->getShippingAddress()->getRegion();
        if ($region instanceof \Magento\Customer\Model\Data\Region) {
            $quote->getShippingAddress()->setRegion($region->getRegion());
        } elseif (is_array($region)) { //M2.2.x
            $quote->getShippingAddress()->setRegion($region['region'] ?: '');
        }
        $region = $quote->getBillingAddress()->getRegion();
        if ($region instanceof \Magento\Customer\Model\Data\Region) {
            $quote->getBillingAddress()->setRegion($region->getRegion());
        } elseif (is_array($region)) { //M2.2.x
            $quote->getBillingAddress()->setRegion($region['region'] ?: '');
        }
    }
}
