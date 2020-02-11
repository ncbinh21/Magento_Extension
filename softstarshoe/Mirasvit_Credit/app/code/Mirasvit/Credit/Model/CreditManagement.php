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


namespace Mirasvit\Credit\Model;

use Mirasvit\Credit\Api\CreditManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;

class CreditManagement implements CreditManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($cartId)
    {
        $quote = $this->cartRepository->get($cartId);
        $quote->setUseCredit(Config::USE_CREDIT_YES)
            ->setBaseCreditAmountUsed(0)
            ->setCreditAmountUsed(0)
            ->collectTotals()
            ->save();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function cancel($cartId)
    {
        $quote = $this->cartRepository->get($cartId);
        $quote->setUseCredit(Config::USE_CREDIT_NO)
            ->setBaseCreditAmountUsed(0)
            ->setCreditAmountUsed(0)
            ->collectTotals()
            ->save();

        return true;
    }
}