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



namespace Mirasvit\Credit\Controller\Account;

use Magento\Framework\Controller\ResultFactory;
use Mirasvit\Credit\Model\Transaction;
use Mirasvit\Credit\Controller\Account;

class Refill extends \Magento\Checkout\Controller\Cart\Add
{
    /**
     *  {@inheritdoc}
     */
    public function execute()
    {
        parent::execute();

        $this->_redirect('checkout/cart/');
    }
}