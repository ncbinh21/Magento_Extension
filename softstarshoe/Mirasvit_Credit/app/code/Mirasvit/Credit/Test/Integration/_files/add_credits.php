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



/** @var $balance \Mirasvit\Credit\Model\Balance */
$balance = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Mirasvit\Credit\Model\Balance');
$balance
    ->setCustomerId(1)
    ->setAmount(333)
    ->setIsSubscribed(1)
    ->save();

/** @var $transaction \Mirasvit\Credit\Model\Transaction */
$transaction = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Mirasvit\Credit\Model\Transaction');
$transaction
    ->setBalanceId($balance->getId())
    ->setBalanceAmount($balance->getAmount())
    ->setBalanceDelta($balance->getAmount())
    ->setAction('manual')
    ->setMessage('test')
    ->setIsNotified(1)
    ->save();
