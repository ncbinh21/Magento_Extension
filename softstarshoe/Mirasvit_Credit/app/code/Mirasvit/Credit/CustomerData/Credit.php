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


namespace Mirasvit\Credit\CustomerData;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Mirasvit\Credit\Model\BalanceFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class Credit implements SectionSourceInterface
{
    /**
     * @var BalanceFactory
     */
    protected $balanceFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @param BalanceFactory $balanceFactory
     * @param Session        $customerSession
     * @param PricingHelper  $pricingHelper
     */
    public function __construct(
        BalanceFactory $balanceFactory,
        Session $customerSession,
        PricingHelper $pricingHelper
    ) {
        $this->balanceFactory = $balanceFactory;
        $this->customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        return [
            'amount' => $this->pricingHelper->currency($this->getBalance()->getAmount(), true, false),
        ];
    }

    /**
     * @return \Mirasvit\Credit\Model\Balance
     */
    public function getBalance()
    {
        return $this->balanceFactory->create()->loadByCustomer($this->customerSession->getCustomerId());
    }
}
